var stream = require('stream');
var bz2 = require('./lib/bzip2');
var bitIterator = require('./lib/bit_iterator');

class Unbzip2Stream extends stream.Transform {
    constructor(options) {

        super(options);

        this.queue = [];
        this.hasBytes = 0;
        this.blockSize = 0;
        this.done = false;
        this.bitReader = null;

    }

    _decompressAndQueue() {
        if (this.destroyed)
            return false;
        try {
            return this._decompressBlock();
        } catch( error ) {
            this.emit('error', error);
            return false;
        }
    }

    _decompressBlock() {
        if(!this.blockSize) {
            this.blockSize = bz2.header(this.bitReader);
            return true;
        } else {
            var length = 100000 * this.blockSize;
            var buffer = new Int32Array(length);
            var bytes = [];
            var push = (value) => {
                bytes.push(value)
            };

            var done = bz2.decompress(this.bitReader, push, buffer, length);

            if(done) {
                this.push(null);
                return false;
            } else {
                this.push(Buffer.from(bytes));
                return true;
            }
        }
    }

    _transform(chunk, encoding, next) {
        this.queue.push(chunk);
        this.hasBytes += chunk.length;

        if(this.bitReader == null) {
            this.bitReader = bitIterator(() => {
                return this.queue.shift();
            });
        }

        while (this.hasBytes - this.bitReader.bytesRead + 1 >= ((25000 + 100000 * this.blockSize) || 4)){
            // console.error('decompressing with', hasBytes - bitReader.bytesRead + 1, 'bytes in buffer');
            if (!this.done) this.done = !this._decompressAndQueue();
            if (this.done) break;
        }

        process.nextTick(next);
    }

    _flush(next) {
        if(!this.done) {
            while(this._decompressAndQueue())
                continue;
        }
    }
}

module.exports = function unbzip2Stream(options) {
    return new Unbzip2Stream(options);
};

module.exports.Stream = Unbzip2Stream;

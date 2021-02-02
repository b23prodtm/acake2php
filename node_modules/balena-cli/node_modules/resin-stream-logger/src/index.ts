import * as _ from 'lodash'

const es = require('event-stream')

export class StreamLogger {

	private prefixes: { [name: string]: string }

	public constructor() {
		// Set up some default loggers
		this.prefixes = {
			'info': '[Info]',
			'warn': '[Warn]',
			'error': '[Error]',
			'success': '[Success]',
			'debug': '[Debug]'
		}
	}

	/**
	 * addPrefix
	 *	add a prefix to the internal prefix object,
	 *	which can then be used to create through streams
	 */
	public addPrefix(name: string, prefix: string) {
		this.prefixes[name] = prefix
	}

	/**
	 * getLogStream
	 *	get a stream that when logged to will split up the info by lines,
	 *	apply a prefix, and forward it on.
	 */
	public createLogStream(prefixName: string)
		: NodeJS.ReadWriteStream {

		const self = this
		return es.pipe(
			es.split(),
			es.through(function(this: NodeJS.ReadWriteStream, data: string) {
				if (data == '')
					return
				this.emit('data', self.formatWithPrefix(prefixName, data))
			})
		)
	}

	/**
	 * formatWithPrefix
	 *	Format a string to send through the stream. This can be used
	 *	standalone for special situations.
	 */
	public formatWithPrefix(name: string, message: string): string {
		const prefix = this.prefixes[name]
		if (!prefix) throw new Error(`Unknown prefix ${name}`)

		const maxLength = this.getMaxPrefixLength()!
		const spaces = _.times(maxLength - prefix.length, _.constant(' ')).join('')
		return `${prefix}${spaces} ${message}\n`
	}

	private getMaxPrefixLength(): number | undefined {
		return _.max(_.map(this.prefixes, (prefix) => prefix.length))
	}

}

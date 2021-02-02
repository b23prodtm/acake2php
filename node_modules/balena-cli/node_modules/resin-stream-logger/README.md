# stream-logger

Stream inputs to logs!

## Initialisation

```
logger = new StreamLogger()

logger.addPrefix('info', colors.blue('[Info]'))
logger.addPrefix('warn', colors.orange('[Warn]'))
logger.addPrefix('error', colors.red('[Error]'))
logger.addPrefix('data', colors.yellow('[Data]'))

```

## Creating a stream

```
const stream = logger.createLogStream('data')

dataSource.pipe(stream)
stream.pipe(process.stdout)
```

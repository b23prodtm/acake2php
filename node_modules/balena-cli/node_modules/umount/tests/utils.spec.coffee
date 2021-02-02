chai = require('chai')
expect = chai.expect
sinon = require('sinon')
chai.use(require('sinon-chai'))

os = require('os')
utils = require('../lib/utils')

describe 'Utils:', ->

	describe '.isWin32()', ->

		describe 'given the platform is win32', ->

			beforeEach ->
				@osPlatformStub = sinon.stub(os, 'platform')
				@osPlatformStub.returns('win32')

			afterEach ->
				@osPlatformStub.restore()

			it 'should return true', ->
				expect(utils.isWin32()).to.be.true

		describe 'given the platform is not win32', ->

			beforeEach ->
				@osPlatformStub = sinon.stub(os, 'platform')
				@osPlatformStub.returns('darwin')

			afterEach ->
				@osPlatformStub.restore()

			it 'should return false', ->
				expect(utils.isWin32()).to.be.false

	describe '.isMacOSX()', ->

		describe 'given the platform is darwin', ->

			beforeEach ->
				@osPlatformStub = sinon.stub(os, 'platform')
				@osPlatformStub.returns('darwin')

			afterEach ->
				@osPlatformStub.restore()

			it 'should return true', ->
				expect(utils.isMacOSX()).to.be.true

		describe 'given the platform is not darwin', ->

			beforeEach ->
				@osPlatformStub = sinon.stub(os, 'platform')
				@osPlatformStub.returns('linux')

			afterEach ->
				@osPlatformStub.restore()

			it 'should return false', ->
				expect(utils.isMacOSX()).to.be.false

	describe '.isLinux()', ->

		describe 'given the platform is linux', ->

			beforeEach ->
				@osPlatformStub = sinon.stub(os, 'platform')
				@osPlatformStub.returns('linux')

			afterEach ->
				@osPlatformStub.restore()

			it 'should return true', ->
				expect(utils.isLinux()).to.be.true

		describe 'given the platform is not linux', ->

			beforeEach ->
				@osPlatformStub = sinon.stub(os, 'platform')
				@osPlatformStub.returns('darwin')

			afterEach ->
				@osPlatformStub.restore()

			it 'should return false', ->
				expect(utils.isLinux()).to.be.false

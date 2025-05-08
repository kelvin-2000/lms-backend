export default {
  transform: {},
  moduleNameMapper: {
    '^(\\.{1,2}/.*)\\.js$': '$1',
  },
  testMatch: ['**/tests/api/**/*.test.js'],
  setupFilesAfterEnv: ['./tests/api/setup.js']
}; 
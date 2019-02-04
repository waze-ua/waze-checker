module.exports = {
  root: true,
  parserOptions: {
    ecmaVersion: 2017,
    sourceType: 'module'
  },
  extends: [
    'eslint:recommended',
    'plugin:ember/recommended'
  ],
  env: {
    browser: true
  },
  rules: {
    'quotes': ['error', 'single'],
    'comma-dangle': ['error', 'always-multiline'],
    // 'key-spacing': ['warn', { 'align': 'value' }],
    // 'array-element-newline': ['error', { 'multiline': true }],
  },
  globals: {
    moment: true
  }
};

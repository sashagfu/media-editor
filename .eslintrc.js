module.exports = {
  root: true,
  parserOptions: {
    sourceType: 'module',
    parser: 'babel-eslint',
    ecmaVersion: 2017,
    ecmaFeatures: {
      jsx: true,
      vue: true,
    },
  },
  env: {
    node: true,
  },
  plugins: [
    'vue',
  ],
  extends: [
    'plugin:vue/recommended',
    '@vue/airbnb',
  ],
  rules: {
    'no-console': 'off',
    'no-debugger': 'off',
    'import/no-unresolved': [
      0,
      {
        commonjs: true,
        amd: true,
      },
    ],
    'import/extensions': [
      'error',
      'ignorePackages',
      {
        js: 'never',
        mjs: 'never',
        jsx: 'never',
        ts: 'never',
        tsx: 'never',
        vue: 'never',
        json: 'never',
      },
    ],
    'function-paren-newline': [
      'error',
      'consistent',
    ],
    'no-underscore-dangle': [
      'error',
      {
        allow:
          [
            '_id',
            '_key',
            '_isVue',
            '__get',
            '__typename',
          ],
      },
    ],
    'vue/attribute-hyphenation': [
      'error',
      'always',
    ],
    'vue/html-end-tags': 'error',
    'vue/html-indent': [
      'error',
      2,
    ],
    'vue/html-self-closing': 'error',
    'vue/require-default-prop': 'error',
    'vue/require-prop-types': 'error',
    'vue/attributes-order': 'error',
    'vue/html-quotes': [
      'error',
      'double',
    ],
    'vue/order-in-components': 'error',
  },
};
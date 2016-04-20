import babel from 'rollup-plugin-babel';

export default {
    moduleName: 'flow-query-api',
    entry: 'Resources/Private/JavaScript/index.js',
    plugins: [babel()],
    format: 'umd',
    dest: 'Resources/Public/JavaScript/FlowQuery.js'
};

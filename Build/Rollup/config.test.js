
import babel from 'rollup-plugin-babel';
import multiEntry from 'rollup-plugin-multi-entry';

export default {
    entry: 'Resources/Private/JavaScript/**/*.spec.js',
    plugins: [babel(), multiEntry()],
    format: 'cjs',
    intro: 'require("source-map-support").install();',
    dest: 'Build/Mocha/bundle.js',
    sourceMap: true
};

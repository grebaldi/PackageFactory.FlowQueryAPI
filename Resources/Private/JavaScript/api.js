import {
	normalizeContextItem
} from './Utils/index';

import * as operations from './Operationss//index';

const dispatch = request => {
	
};

const initializeChainedInterface = (configuration, request) => Object.keys(operations)
.reduce(
	(operationName, chainedInterface) => {
		const operation = operations[operationName](configuration, request);
		chainedInterface[operationName] = (...args) => {
			const rechain = operation(..args);
			return rechain(
				request => initializeChainedInterface(configuration, request),
				dispatch
			);
		};

		return chainedInterface;
	},
	{}
);

export default createFlowQueryAPI = configuration => {
	const q = (context) => {
		if (!Array.isArray()) {
			context = [context];
		}

		return {
			context: context.map(normalizeContextItem),
			chain: []
		};
	};

	return {q};
};

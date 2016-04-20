import {
	normalizeContextItem
} from './Utils/index';

import {operations} from './Operations/index';

const createDispatch = configuration => request => {
	// validate the request
	// send it to the server
	// convert the response
	// handle errors
	console.log(request);
};

const initializeChainedInterface = (configuration, request) => Object.keys(operations).reduce(
	(chainedInterface, operationName) => {
		const operation = operations[operationName](configuration, request);
		chainedInterface[operationName] = (...args) => {
			const rechain = operation(...args);
			return rechain(
				request => initializeChainedInterface(configuration, request),
				createDispatch(configuration)
			);
		};

		return chainedInterface;
	},
	{}
);

export default configuration => {
	const q = (context) => {
		if (!Array.isArray()) {
			context = [context];
		}

		return initializeChainedInterface(configuration, {
			context: context.map(normalizeContextItem),
			chain: []
		});
	};

	return {q};
};

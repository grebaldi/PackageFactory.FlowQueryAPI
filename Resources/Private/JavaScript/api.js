import {
	normalizeContextItem
} from './Utils/index';

import {operations} from './Operations/index';

const createDispatch = configuration => request => {
	fetch(configuration.endpoints.query, {
		method: 'POST',
		body: JSON.stringify({
			q: request,
			finisher: request.finisher,
			finisherArguments: request.finisherArguments
		}),
		headers: {
			'Content-Type': 'application/json'
		}
	})
	.then(res => res.json())
	.then(res => console.log(res));
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

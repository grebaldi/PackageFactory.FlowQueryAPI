//
// Find FlowQuery operation
//
export default (configuration, request) => filter => forward => {
    request.chain.push({
        type: 'find',
        filter
    });

    return forward(request);
};

//
// FlowQuery `filter` operation
//
export default (configuration, request) => filter => forward => {
    request.chain.push({
        type: 'filter',
        filter
    });

    return forward(request);
};

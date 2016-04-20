//
// FlowQuery `children` operation
//
export default (configuration, request) => filter => forward => {
    request.chain.push({
        type: 'children',
        filter
    });

    return forward(request);
};

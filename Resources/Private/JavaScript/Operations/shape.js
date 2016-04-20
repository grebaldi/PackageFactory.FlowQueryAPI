//
// FlowQuery `shape` operation
//
export default (configuration, request) => shape => forward => {
    request.chain.push({
        type: 'shape',
        shape
    });

    return forward(request);
};

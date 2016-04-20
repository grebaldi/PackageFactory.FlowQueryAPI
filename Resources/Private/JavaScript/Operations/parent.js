//
// FlowQuery `parents` operation
//
export default (configuration, request) => () => forward => {
    request.chain.push({
        type: 'parent'
    });

    return forward(request);
};

//
// FlowQuery `parent` operation
//
export default (configuration, request) => filter => forward => {
    request.chain.push({
        type: 'parent'
    });

    return forward(request);
};

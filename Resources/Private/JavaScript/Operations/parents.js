//
// FlowQuery `parents` operation
//
export default (configuration, request) => filter => forward => {
    request.chain.push({
        type: 'parents',
        filter
    });

    return forward(request);
};

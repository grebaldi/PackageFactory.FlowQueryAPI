//
// FlowQuery `closest` operation
//
export default (configuration, request) => filter => forward => {
    request.chain.push({
        type: 'closest',
        filter
    });

    return forward(request);
};

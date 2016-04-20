//
// FlowQuery `is` finisher
//
export default (configuration, request) => filter => (forward, dispatch) => {
    request.finisher = 'is';
    request.finisherArguments = [filter];

    return dispatch(request);
};

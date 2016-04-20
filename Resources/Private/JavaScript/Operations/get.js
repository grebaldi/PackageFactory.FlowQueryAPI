//
// FlowQuery `get` finisher
//
export default (configuration, request) => index => (forward, dispatch) => {
    request.finisher = 'get';

    if (index !== undefined) {
        request.finisherArguments = [index];
    }

    return dispatch(request);
};

//
// FlowQuery `count` finisher
//
export default (configuration, request) => () => (forward, dispatch) => {
    request.finisher = 'count';

    return dispatch(request);
};

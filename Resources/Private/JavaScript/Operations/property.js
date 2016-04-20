//
// FlowQuery `property` finisher
//
export default (configuration, request) => propertyName => (forward, dispatch) => {
    request.finisher = 'property';
    request.finisherArguments = [propertyName];

    return dispatch(request);
};

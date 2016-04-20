export default contextItem => {
    if (typeof contextItem === 'string') {
        return contextItem;
    }

    if (typeof contextItem.contextPath === 'string') {
        return contextItem.contextPath;
    }

    console.error('[FlowQueryAPI]: Received an invalid FlowQuery context', contextItem);
    console.error('[FlowQueryAPI]: A FlowQuery context must either be a string, an object with a contextPath property or an array of those items.');
};

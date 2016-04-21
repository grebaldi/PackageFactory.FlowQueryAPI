# PackageFactory.FlowQueryAPI

## Compile sources

```
cd Packages/Appplication/PackageFactory.FlowQueryAPI
nvm use v4
npm install
npm run build
```

## Include routes in `Configuration/Routes.yaml`:

```
-
  name: 'FlowQuery'
  uriPattern: '<FlowQueryAPISubroutes>'
  subRoutes:
    'FlowQueryAPISubroutes':
      package: 'PackageFactory.FlowQueryAPI'
```

## Include

```html
<script src="{f:uri.resource(path: 'resource://PackageFactory.FlowQueryAPI/Public/JavaScript/FlowQuery.js')}"></script>
```

```js
var q = window.neos.q;
var documents = q('site').find('[instanceof TYPO3.Neos:Content]').shape({
		contextPath: 'contextPath',
		properties: 'properties'
}).get();

documents.then(function(documents) {
		var list = documents.filter(node => node.properties.text).reduce(function(string, document) {
				string += '<li>' + document.properties.text + '</li>';
				return string;
		}, '');

		document.querySelector('body > .container').innerHTML = '<ul>' + list + '</ul>';

		return q(documents[0]).find('[instanceof TYPO3.Neos:Content]').count();
}).then(function(numberOfDocuments) {
		document.querySelector('body > .container').innerHTML += '<strong>' + numberOfDocuments + '</strong>';
});
```

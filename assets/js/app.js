import '../css/app.scss';

import App from './react/App';
import ReactDOM from 'react-dom';
import React from 'react';

let element = React.createElement(App);
ReactDOM.render(element, document.getElementById('root'));

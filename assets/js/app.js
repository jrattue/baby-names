import '../css/app.scss';

import App from './react/App';
import ReactDOM from 'react-dom';
import React from 'react';

let element = React.createElement(App);
const container = document.getElementById('root');
if(container) {
    ReactDOM.render(element,container);
}

const menuToggle = document.querySelector('header span.menu-toggle')

menuToggle.addEventListener('click', () => {
    document.querySelector('nav').classList.toggle('active');
})
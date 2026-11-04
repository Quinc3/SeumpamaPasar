import './bootstrap';
import Alpine from 'alpinejs';
import { driver as Driver } from 'driver.js';
import 'driver.js/dist/driver.css';

window.Alpine = Alpine;
window.Driver = Driver;

Alpine.start();
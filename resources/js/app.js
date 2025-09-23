import './bootstrap';
import {
    Chart,
    LineElement,
    BarElement,
    DoughnutController,
    LineController,
    BarController,
    CategoryScale,
    LinearScale,
    PointElement,
    ArcElement,
    Title,
    Tooltip,
    Legend,
    Filler
} from 'chart.js';

// Register Chart.js components
Chart.register(
    LineElement,
    BarElement,
    DoughnutController,
    LineController,
    BarController,
    CategoryScale,
    LinearScale,
    PointElement,
    ArcElement,
    Title,
    Tooltip,
    Legend,
    Filler
);

// Make Chart.js globally available
window.Chart = Chart;
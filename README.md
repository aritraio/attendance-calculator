# Attendance Predictor

![Attendance Predictor Screenshot](assets/screenshot.png)

## Overview

A lightweight web application that calculates attendance percentages based on a customizable weekly class routine, planned leave duration, and optional class attendance during leave. Originally built in PHP, this version showcases a modern, responsive UI using vanilla HTML, CSS, and JavaScript.

## Table of Contents
- [Features](#features)
- [Demo](#demo)
- [Installation](#installation)
- [Usage](#usage)
- [Architecture](#architecture)
- [Contributing](#contributing)
- [License](#license)

## Features
- **Dynamic Schedule Input**: Define your weekly class schedule.
- **Leave Calculation**: Specify leave days and whether you attend classes during leave.
- **Real‑time Result**: Instantly see the projected attendance percentage.
- **Responsive Design**: Works on desktop and mobile devices.
- **Pure Front‑end**: No server‑side dependencies; all logic runs in the browser.

## Demo

Try the live demo by opening `index.html` in your browser.

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/aritraio/attendance-precdictor.git
   ```
2. Open the project folder and serve the files (optional):
   ```bash
   npx -y serve .
   ```
   Or simply open `index.html` directly in a browser.

## Usage

1. Fill in your weekly routine (e.g., number of classes per day).
2. Enter the total number of leave days.
3. Indicate whether you will attend classes during leave.
4. Click **Calculate** to view the projected attendance percentage.

## Architecture

- **index.html** – Main markup.
- **style.css** – Styling with a modern glassmorphism aesthetic.
- **app.js** – Core calculation logic and UI interactions.

## Contributing

Contributions are welcome! Please fork the repository, create a feature branch, and submit a pull request.

## License

This project is licensed under the MIT License.
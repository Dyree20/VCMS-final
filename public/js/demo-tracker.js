/**
 * Demo Location Simulator for Testing
 * 
 * This allows testing the tracking system without real GPS
 * Use in browser console to simulate officer movements
 * 
 * Usage:
 *   DemoTracker.simulateRoute()    - Simulate officer walking through city
 *   DemoTracker.jumpToLocation(lat, lon)  - Jump to specific location
 *   DemoTracker.stop()             - Stop simulation
 */

const DemoTracker = (function() {
    // Manila city coordinates and areas
    const LOCATIONS = {
        makati: { lat: 14.5549, lon: 121.0193, name: 'Makati' },
        bgc: { lat: 14.5630, lon: 121.0435, name: 'BGC' },
        ortigas: { lat: 14.5894, lon: 121.0629, name: 'Ortigas' },
        quezoncity: { lat: 14.6349, lon: 121.0388, name: 'Quezon City' },
        manila: { lat: 14.5995, lon: 121.0012, name: 'Manila' },
        pasig: { lat: 14.5794, lon: 121.0802, name: 'Pasig' },
        malate: { lat: 14.5600, lon: 120.9831, name: 'Malate' },
        intramuros: { lat: 14.5913, lon: 120.9740, name: 'Intramuros' },
    };

    let state = {
        isSimulating: false,
        simulationInterval: null,
        currentRoute: [],
        routeIndex: 0,
        startLocation: LOCATIONS.manila,
        endLocation: LOCATIONS.makati,
    };

    /**
     * Generate route between two locations
     */
    function generateRoute(from, to, steps = 20) {
        const route = [];
        const latDiff = to.lat - from.lat;
        const lonDiff = to.lon - from.lon;

        for (let i = 0; i <= steps; i++) {
            const progress = i / steps;
            route.push({
                lat: from.lat + (latDiff * progress),
                lon: from.lon + (lonDiff * progress),
                name: `${from.name} → ${to.name}`,
            });
        }

        return route;
    }

    /**
     * Send simulated location to server
     */
    function sendLocation(latitude, longitude, accuracy = 15) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch('/gps/update-location', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                latitude: parseFloat(latitude.toFixed(8)),
                longitude: parseFloat(longitude.toFixed(8)),
                accuracy: accuracy,
                address: `Demo Location (${latitude.toFixed(4)}, ${longitude.toFixed(4)})`,
            }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log(`✓ Demo location sent: ${latitude.toFixed(4)}, ${longitude.toFixed(4)}`);
                } else {
                    console.warn('Failed to send demo location:', data);
                }
            })
            .catch(error => {
                console.error('Error sending demo location:', error);
            });
    }

    /**
     * Simulate officer moving along a route
     */
    function simulateRoute(fromLocation = LOCATIONS.manila, toLocation = LOCATIONS.makati, steps = 30) {
        if (state.isSimulating) {
            console.warn('Simulation already running');
            return;
        }

        console.log(`🚗 Starting route simulation: ${fromLocation.name} → ${toLocation.name}`);
        state.isSimulating = true;
        state.currentRoute = generateRoute(fromLocation, toLocation, steps);
        state.routeIndex = 0;

        state.simulationInterval = setInterval(() => {
            if (state.routeIndex < state.currentRoute.length) {
                const location = state.currentRoute[state.routeIndex];
                sendLocation(location.lat, location.lon);

                console.log(`📍 Step ${state.routeIndex + 1}/${state.currentRoute.length}: ${location.name}`);
                state.routeIndex++;
            } else {
                console.log('✅ Route simulation complete');
                stop();
            }
        }, 2000); // Send every 2 seconds
    }

    /**
     * Jump to specific location
     */
    function jumpToLocation(latitude, longitude, name = 'Custom Location') {
        console.log(`📍 Jumping to: ${name} (${latitude}, ${longitude})`);
        sendLocation(latitude, longitude);
    }

    /**
     * Simulate random movement around a location
     */
    function simulateRandomMovement(centerLocation = LOCATIONS.makati, radiusKm = 1, duration = 60000) {
        if (state.isSimulating) {
            console.warn('Simulation already running');
            return;
        }

        console.log(`🎲 Random movement around ${centerLocation.name} for ${duration / 1000} seconds`);
        state.isSimulating = true;

        const startTime = Date.now();

        state.simulationInterval = setInterval(() => {
            const elapsed = Date.now() - startTime;

            if (elapsed < duration) {
                // Generate random point within radius
                const angle = Math.random() * Math.PI * 2;
                const distance = Math.random() * radiusKm / 111; // 1 degree ≈ 111 km

                const lat = centerLocation.lat + (Math.cos(angle) * distance);
                const lon = centerLocation.lon + (Math.sin(angle) * distance);

                sendLocation(lat, lon);
                console.log(`📍 Random position: (${lat.toFixed(4)}, ${lon.toFixed(4)})`);
            } else {
                console.log('✅ Random movement simulation complete');
                stop();
            }
        }, 3000); // Send every 3 seconds
    }

    /**
     * Simulate circular patrol route
     */
    function simulateCircularPatrol(centerLocation = LOCATIONS.makati, radiusKm = 0.5, steps = 30) {
        if (state.isSimulating) {
            console.warn('Simulation already running');
            return;
        }

        console.log(`🔄 Circular patrol around ${centerLocation.name}`);
        state.isSimulating = true;
        state.routeIndex = 0;

        // Generate circular route
        state.currentRoute = [];
        for (let i = 0; i < steps; i++) {
            const angle = (i / steps) * Math.PI * 2;
            const distance = radiusKm / 111; // Convert km to degrees

            state.currentRoute.push({
                lat: centerLocation.lat + (Math.sin(angle) * distance),
                lon: centerLocation.lon + (Math.cos(angle) * distance),
            });
        }

        state.simulationInterval = setInterval(() => {
            if (state.routeIndex < state.currentRoute.length) {
                const location = state.currentRoute[state.routeIndex];
                sendLocation(location.lat, location.lon);

                console.log(`🔄 Patrol step ${state.routeIndex + 1}/${state.currentRoute.length}`);
                state.routeIndex++;
            } else {
                state.routeIndex = 0; // Loop the patrol
            }
        }, 2000);
    }

    /**
     * Stop simulation
     */
    function stop() {
        if (!state.isSimulating) {
            console.warn('No simulation running');
            return;
        }

        console.log('⏹️ Stopping simulation');
        state.isSimulating = false;

        if (state.simulationInterval) {
            clearInterval(state.simulationInterval);
            state.simulationInterval = null;
        }
    }

    /**
     * Show available demo locations
     */
    function showLocations() {
        console.log('📍 Available Demo Locations:');
        console.log('==========================');
        Object.entries(LOCATIONS).forEach(([key, loc]) => {
            console.log(`  ${key}: ${loc.name} (${loc.lat}, ${loc.lon})`);
        });
        console.log('\nUsage: DemoTracker.jumpToLocation(lat, lon, "name")');
    }

    /**
     * Show available commands
     */
    function help() {
        console.log(`
🚗 DEMO TRACKER - Testing Commands
===================================

SIMULATE ROUTES:
  DemoTracker.simulateRoute()
    → Walk from Manila to Makati
  
  DemoTracker.simulateRoute(DemoTracker.LOCATIONS.bgc, DemoTracker.LOCATIONS.ortigas)
    → Walk from BGC to Ortigas

RANDOM MOVEMENT:
  DemoTracker.simulateRandomMovement()
    → Random movement around Makati for 1 minute
  
  DemoTracker.simulateRandomMovement(DemoTracker.LOCATIONS.quezoncity, 2, 120000)
    → Random movement around QC, 2km radius, 2 minutes

CIRCULAR PATROL:
  DemoTracker.simulateCircularPatrol()
    → Walk in circle around Makati
  
  DemoTracker.simulateCircularPatrol(DemoTracker.LOCATIONS.intramuros, 1)
    → Walk in circle around Intramuros, 1km radius

JUMP TO LOCATION:
  DemoTracker.jumpToLocation(14.5995, 121.0012)
    → Jump to coordinates
  
  DemoTracker.jumpToLocation(14.5995, 121.0012, "My Location")
    → Jump with custom name

STOP:
  DemoTracker.stop()
    → Stop any running simulation

VIEW:
  DemoTracker.showLocations()
    → List all demo locations
  
  DemoTracker.getStatus()
    → Get current status

LOCATIONS:
  DemoTracker.LOCATIONS
    → See available locations with coordinates
        `);
    }

    /**
     * Get current status
     */
    function getStatus() {
        console.log('Status:', {
            isSimulating: state.isSimulating,
            routeProgress: `${state.routeIndex}/${state.currentRoute.length}`,
            currentRoute: state.currentRoute,
        });
    }

    // Public API
    return {
        // Methods
        simulateRoute: simulateRoute,
        simulateRandomMovement: simulateRandomMovement,
        simulateCircularPatrol: simulateCircularPatrol,
        jumpToLocation: jumpToLocation,
        stop: stop,
        showLocations: showLocations,
        getStatus: getStatus,
        help: help,

        // Constants
        LOCATIONS: LOCATIONS,
    };
})();

console.log('📍 Demo Tracker loaded! Type DemoTracker.help() to see commands.');

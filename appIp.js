const os = require('os');

function getLocalIP() {
    const networkInterfaces = os.networkInterfaces();
    
    for (const interfaceName in networkInterfaces) {
        const networkInterface = networkInterfaces[interfaceName];
        
        for (const net of networkInterface) {
            // Check if the address is IPv4 and not internal (i.e., not a loopback address)
            if (net.family === 'IPv4' && !net.internal) {
                return net.address;
            }
        }
    }
    return 'Unable to find local IP';
}

console.log('Local IP:', `http://${getLocalIP()}`);
const { spawn } = require('child_process');

function runMcpTool(name, args, id) {
  return new Promise((resolve, reject) => {
    console.log(`Calling ${name} with args:`, JSON.stringify(args));
    const child = spawn('npx', ['hostinger-api-mcp@latest'], {
      shell: true,
      env: {
        ...process.env,
        API_TOKEN: 'GIFlklPNq4zmUsGChBOcCipqssWiT1ukLED2DuC080d5e38e'
      }
    });

    let buffer = '';

    child.stdout.on('data', (data) => {
      buffer += data.toString();
      try {
        const lines = buffer.split('\n');
        for (let i = 0; i < lines.length - 1; i++) {
          const line = lines[i].trim();
          if (line) {
            const response = JSON.parse(line);
            if (response.id === id) {
              child.kill();
              resolve(response.result);
              return;
            }
          }
        }
        buffer = lines[lines.length - 1];
      } catch (err) {}
    });

    child.stderr.on('data', (data) => {
      // console.error('STDERR:', data.toString());
    });

    child.on('close', (code) => {
      if (code !== 0 && code !== null) {
        reject(new Error(`Process exited with code ${code}`));
      }
    });

    const request = {
      jsonrpc: '2.0',
      method: 'tools/call',
      params: {
        name,
        arguments: args
      },
      id
    };

    setTimeout(() => {
      child.stdin.write(JSON.stringify(request) + '\n');
    }, 2000);
  });
}

async function main() {
  try {
    console.log('Recreating main website: sdcolourslab.in...');
    const mainResult = await runMcpTool('hosting_createWebsiteV1', {
      domain: 'sdcolourslab.in',
      order_id: 1009111881,
      datacenter_code: 'mumbai'
    }, 10);
    console.log('Main Website Creation Result:', JSON.stringify(mainResult, null, 2));

    console.log('\nWaiting 5 seconds...');
    await new Promise(resolve => setTimeout(resolve, 5000));

    console.log('Recreating subdomain: backend.sdcolourslab.in...');
    const subResult = await runMcpTool('hosting_createWebsiteV1', {
      domain: 'backend.sdcolourslab.in',
      order_id: 1009111881
    }, 11);
    console.log('Subdomain Creation Result:', JSON.stringify(subResult, null, 2));
  } catch (error) {
    console.error('Error during website creation:', error);
  }
}

main();

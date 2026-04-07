import { copyFile, mkdir } from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const assetsDir = path.resolve(__dirname, '../public/build/assets');

const aliases = new Map([
    ['app.css', ['app-DtLkrWkJ.css', 'app-CoZptJTJ.css']],
    ['app2.js', ['app-eMHK6VFw.js']],
]);

await mkdir(assetsDir, { recursive: true });

for (const [sourceName, aliasNames] of aliases) {
    const sourcePath = path.join(assetsDir, sourceName);

    for (const aliasName of aliasNames) {
        const aliasPath = path.join(assetsDir, aliasName);
        await copyFile(sourcePath, aliasPath);
    }
}

console.log('Created legacy asset aliases for cached production HTML.');

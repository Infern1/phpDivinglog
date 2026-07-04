import { cpSync, mkdirSync } from 'node:fs';
import { dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = dirname(dirname(fileURLToPath(import.meta.url)));

mkdirSync(`${root}/public/assets/vendor`, { recursive: true });
cpSync(`${root}/public/assets/js`, `${root}/public/assets/vendor/js`, { recursive: true });
cpSync(`${root}/public/assets/css`, `${root}/public/assets/vendor/css`, { recursive: true });

console.log('Assets copied to public/assets/vendor');

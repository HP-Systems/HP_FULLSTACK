import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import fs from 'fs';

const getFiles = (dir, fileList = []) => {
    const files = fs.readdirSync(dir);
    files.forEach(file => {
        if (fs.statSync(path.join(dir, file)).isDirectory()) {
            fileList = getFiles(path.join(dir, file), fileList);
        } else {
            fileList.push(path.join(dir, file));
        }
    });
    return fileList;
};

const cssFiles = getFiles('resources/css');
const jsFiles = getFiles('resources/js');
const input = [...cssFiles, ...jsFiles];

export default defineConfig({
    plugins: [
        laravel({
            input: input,
            refresh: true,
        }),
    ],
});

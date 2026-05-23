import {defineConfig} from 'vite';
import {resolve} from 'path';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import path from 'path';
import fs from 'fs';
import crypto from 'crypto';

const filesFromLibraries = {
    'node_modules/bootstrap/dist/js/bootstrap.min.js': 'bootstrap',
    'node_modules/highcharts/highcharts.js': 'highcharts',
    'node_modules/leaflet/dist/leaflet.js' : 'leaflet',
    'node_modules/leaflet.markercluster/dist/leaflet.markercluster.js': 'leaflet.markercluster',
    'node_modules/tablesort/dist/tablesort.min.js': 'tablesort',
};

const filesFromLibrariesForConfiguration = [];
const filesFromLibrariesForManifest = [];
for (const [sourceFilePath, fileName] of Object.entries(filesFromLibraries)) {
    let hashedFileNameWithExtension = generateHashedFileName(fileName, sourceFilePath);
    let rename = {
        name: hashedFileNameWithExtension,
        stripBase: true,
    };
    filesFromLibrariesForConfiguration.push({
        src: sourceFilePath,
        dest: 'lib',
        // rename relevant for viteStaticCopy configuration!
        rename: () => rename,
    });
    filesFromLibrariesForManifest[sourceFilePath] = {
        file: `lib/${hashedFileNameWithExtension}`,
        src: sourceFilePath,
        isEntry: true,
    };
}

function generateHashedFileName(fileName, fullPath) {
    const fileExtension = path.extname(fullPath);
    const fileBuffer = fs.readFileSync(fullPath);
    const hash = crypto.createHash('sha256').update(fileBuffer).digest('hex');
    return `${fileName}.${hash}${fileExtension}`;
}

export default defineConfig({
    base: '/assets/',
    build: {
        outDir: 'public/assets',
        assetsDir: '',
        manifest: true,
        emptyOutDir: true,
        rollupOptions: {
            input: {
                style: resolve(__dirname, 'resources/css/style.scss'),
                leafletBundle: resolve(__dirname, 'resources/css/leaflet-bundle.scss'),
                ...fs.readdirSync('resources/js', {withFileTypes: true})
                    .filter(f => !f.isDirectory())
                    .map(f => f.name)
                    .map(f => `resources/js/${f}`),
            },
            output: {
                entryFileNames: 'js/[name].[hash].js',
                chunkFileNames: 'js/[name].[hash].js',
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name.endsWith('.css')) {
                        return 'css/[name].[hash][extname]';
                    }
                    if (assetInfo.name.endsWith('.woff2')) {
                        return 'webfonts/[name].[hash][extname]';
                    }
                    return '[name].[hash][extname]';
                },
            },
        },
    },
    publicDir: false,
    plugins: [
        // Copy static files from libraries...
        viteStaticCopy({
            targets: [
                ...filesFromLibrariesForConfiguration,
                {
                    src: 'node_modules/leaflet/dist/images/layers.png',
                    dest: 'lib/images',
                    rename: {
                        stripBase: true,
                    },
                },
                {
                    src: 'node_modules/leaflet/dist/images/layers-2x.png',
                    dest: 'lib/images',
                    rename: {
                        stripBase: true,
                    },
                },
            ],
        }),
        // ... and add their hash-based file names to the manifest file.
        {
            name: 'add-copied-files-to-manifest',
            closeBundle() {
                const manifestPath = path.resolve(__dirname, 'public/assets/.vite/manifest.json');
                let manifest = {
                    ...JSON.parse(fs.readFileSync(manifestPath, 'utf-8')),
                    ...filesFromLibrariesForManifest
                };
                fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 2));
            }
        },
    ],
    // Configure how SCSS is preprocessed.
    css: {
        preprocessorOptions: {
            scss: {
                quietDeps: true,
                silenceDeprecations: [
                    // Bootstrap framework is still using deprecated syntax.
                    'color-functions',
                    'import',
                    'global-builtin',
                    'if-function',
                ],
            },
        },
    },
});

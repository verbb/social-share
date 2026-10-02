import { resolve } from 'node:path';

import { defineConfig } from 'vite';

const builds = {
    cp: {
        root: resolve(import.meta.dirname, 'src/web/assets/cp'),
        input: 'social-share.css',
    },
    frontend: {
        root: resolve(import.meta.dirname, 'src/web/assets/frontend'),
        input: 'social-buttons.css',
    },
};

export default defineConfig(({ mode }) => {
    const currentBuild = builds[mode];

    if (!currentBuild) {
        throw new Error(`Unknown asset build mode: ${mode}`);
    }

    return {
        root: currentBuild.root,
        input: resolve(currentBuild.root, 'src', currentBuild.input),
        build: {
            outDir: resolve(currentBuild.root, 'dist'),
            emptyOutDir: true,
            assetsDir: '',
            cssMinify: 'esbuild',
            cssTarget: ['chrome61', 'safari10'],
            rolldownOptions: {
                output: {
                    assetFileNames: '[name][extname]',
                },
            },
        },
    };
});

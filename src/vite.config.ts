import react from '@vitejs/plugin-react';
import autoprefixer from 'autoprefixer';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from 'tailwindcss';
import { defineConfig, loadEnv } from 'vite';
import dynamicImport from 'vite-plugin-dynamic-import';

// https://vitejs.dev/config/
export default defineConfig((config) => {
  // Load env file based on `mode` in the current working directory.
  // https://main.vitejs.dev/config/#using-environment-variables-in-config
  const env = loadEnv(config.mode, process.cwd(), '');

  return {
    server: {
      hmr: {
        host: 'localhost',
      },
    },
    define: {
      __APP_ENV__: JSON.stringify(env.APP_ENV),
    },
    plugins: [
      laravel({
        input: ['resources/ts/index.scss', 'resources/ts/main.tsx'],
        refresh: true,
        // @ts-ignore
        postcss: [tailwindcss(), autoprefixer()],
      }),
      react({
        babel: {
          plugins: ['babel-plugin-macros'],
        },
      }),
      dynamicImport(),
    ],
    assetsInclude: ['**/*.md'],
    css: {
      preprocessorOptions: {
        scss: {},
      },
    },
    resolve: {
      alias: {
        '@': path.resolve(__dirname, 'resources/ts'),
      },
    },
  };
});

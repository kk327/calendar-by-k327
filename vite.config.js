import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import usePHP from 'vite-plugin-php'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    usePHP({
      entry: [
        "index.php",
        "php/load.php",
        "php/save.php",
        "php/login.php",
        "php/register.php",
        "php/connectionConfig.php"
      ]
    })
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  }
})

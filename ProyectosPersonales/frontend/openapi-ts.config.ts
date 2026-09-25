import { defineConfig } from '@hey-api/openapi-ts';

// Genera libs/api-client/src/generated desde el contrato. `npm run api:generate`.
export default defineConfig({
  input: '../contracts/openapi.yaml',
  output: { path: 'libs/api-client/src/generated', format: 'prettier' },
  plugins: ['@hey-api/client-fetch', '@hey-api/typescript', '@hey-api/sdk'],
});

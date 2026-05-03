import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";

export default defineConfig({
  plugins: [react()],
  // Hosted under https://<domain>/app/
  base: "/app/",
  server: {
    port: 5173
  }
});

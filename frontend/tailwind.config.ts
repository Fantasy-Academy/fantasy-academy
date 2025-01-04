import type { Config } from 'tailwindcss'

const config: Config = {
  content: [
    "./app/**/*.{js,ts,jsx,tsx,mdx}",
    "./pages/**/*.{js,ts,jsx,tsx,mdx}",
    "./components/**/*.{js,ts,jsx,tsx,mdx}",
    "./components/**/*.{js,ts,jsx,tsx,mdx}",
  ],
  theme: {
    extend: {
      backgroundImage: {
        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
        'gradient-conic':
          'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
      },
      fontFamily: {
        sans: ["Open Sans", "sans-serif"],
        alexandria: ["Alexandria", "sans-serif"],
        bebasNeue: ["Bebas Neue", "serif"],
        sourceSans3: ["Source Sans 3", "serif"],
        nunito: ["Nunito", "serif"]
      },
      colors: {
        blueBlack: '#2A313A',
        pureWhite: '#FFFFFF',
        coolGray: '#94A3B8',
        charcoal: '#3C4858',
        vibrantCoral: '#FF6F61',
        goldenYellow: '#FFD700'
      },
    },
  },
  plugins: [],
}
export default config

module.exports = {
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      backgroundImage: {
        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
        'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
      },
      fontFamily: {
        sans: ['Open Sans', 'sans-serif'],
        alexandria: ['Alexandria', 'sans-serif'],
        bebasNeue: ['Bebas Neue', 'sans-serif'],
        sourceSans3: ['Source Sans 3', 'sans-serif'], // (oprav. překlep san-serif -> sans-serif)
        nunito: ['Nunito', 'sans-serif'],
        caveat: ['Caveat', 'sans-serif'],
      },
      colors: {
        blueBlack: '#2A313A',
        ultraBlack: '#000000ff',
        coolGray: '#94A3B8',
        darkWhite: '#F0F4F8',
        charcoal: '#3C4858',
        vibrantCoral: '#FF6F61',
        goldenYellow: '#FFD700',
        pistachio: '#A8D5BA',
      },
      boxShadow: {
        sharp: '6px 6px 0px 0px #94A3B8',
        main: '6px 6px 0px 0px #3C4858',
      },
    },
  },
  plugins: [],
};
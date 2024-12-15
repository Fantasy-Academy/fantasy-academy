import React from 'react';

const BackgroundLayout = () => {
    return (
        <div>
            <svg width="611" height="832" viewBox="0 0 611 832" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g filter="url(#filter0_d_81_1312)">
                    <rect x="163.659" y="-124.322" width="458.819" height="964.428" transform="rotate(15 163.659 -124.322)" fill="url(#paint0_linear_81_1312)" />
                </g>
                <defs>
                    <filter id="filter0_d_81_1312" x="-89.9531" y="-124.322" width="700.798" height="1058.32" filterUnits="userSpaceOnUse" colorInterpolationFilters="sRGB">
                        <feFlood floodOpacity="0" result="BackgroundImageFix" />
                        <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                        <feOffset dy="4" />
                        <feGaussianBlur stdDeviation="2" />
                        <feComposite in2="hardAlpha" operator="out" />
                        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0" />
                        <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_81_1312" />
                        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_81_1312" result="shape" />
                    </filter>
                    <linearGradient id="paint0_linear_81_1312" x1="305.641" y1="122.127" x2="844.423" y2="409.32" gradientUnits="userSpaceOnUse">
                        <stop stopColor="#13EC9C" />
                        <stop offset="1" stopColor="#13C0EC" />
                    </linearGradient>
                </defs>
            </svg>
        </div>
    );
}

export default BackgroundLayout;
'use client'
import React, { useEffect, useState } from "react";
import Btn from "../../components/button/Btn";

const Page = () => {
    const [scrollY, setScrollY] = useState(0);

    useEffect(() => {
        const handleScroll = () => setScrollY(window.scrollY);
        window.addEventListener("scroll", handleScroll);
        return () => window.removeEventListener("scroll", handleScroll);
    }, []);

    // Paralax rychlost
    const parallaxSpeed = 0.3;

    return (
        <div>
            <div className="flex flex-col md:grid md:grid-cols-2 items-center justify-center h-screen px-8 md:px-32 text-center md:text-left">
                {/* Levá část - Text */}
                <div className="mb-8 md:mb-0">
                    <h1 className="font-bebasNeue text-6xl md:text-9xl text-blueBlack">
                        Bet LIKE A PRO
                    </h1>
                    <h3 className="font-caveat text-3xl md:text-5xl text-blueBlack">
                        Training arena for smart bettors.
                    </h3>
                </div>

                {/* Pravá část - Button & Image */}
                <div className="relative flex flex-col items-center">
                    {/* Obrázek */}
                    <img
                        src="/assets/landingImg.png"
                        alt="image"
                        className="scale-[100%] sm:scale-[110%] md:scale-[120%] -translate-y-4 md:-translate-y-6 max-w-[70%] md:max-w-full"
                    />

                    {/* Absolutní div obalující tlačítko */}
                    <div className="absolute bottom-0 left-1/2 -translate-x-1/2 -translate-y-1/4 w-fit">
                        <Link href={"/signup"}>
                            <Btn
                                button={{
                                    text: "Join Now!",
                                    type: "button",
                                    className: "text-lg md:text-xl bg-goldenYellow text-blueBlack px-5 py-4 md:px-7 md:py-6 rotate-[4deg] shadow-main w-64 md:w-80"
                                }}
                            />
                        </Link>
                    </div>

                    {/* Prostřední obrázek */}
                    <div className="w-1/3 h-full">
                        <img
                            src="/assets/center.jpg"
                            alt="Center"
                            className="w-full h-full object-cover"
                        />
                    </div>

                    {/* Pravý obrázek */}
                    <div
                        className="relative w-1/3 h-full"
                        style={{
                            transform: `translateY(${scrollY * parallaxSpeed}px)`,
                            transition: "transform 0.1s linear",
                        }}
                    >
                        <img
                            src="/assets/right.jpg"
                            alt="Right"
                            className="w-full h-full object-cover"
                        />
                    </div>
                </div>

                {/* Text v popředí */}
                <div
                    className="absolute inset-0 flex items-center justify-center text-white text-center px-4 z-10"
                    style={{
                        transform: `translateY(${scrollY * 0.1}px)`,
                        transition: "transform 0.1s linear",
                    }}
                >
                    <div>
                        <h1 className="text-6xl md:text-8xl font-bold mb-6 drop-shadow-lg tracking-wide font-bebasNeue">
                            Fantasy Academy
                        </h1>
                        <p className="text-2xl md:text-4xl mb-8 drop-shadow-md font-caveat">
                            Training arena form smart bettors
                        </p>
                        <Btn>Join Now!</Btn>
                    </div>
                </div>
            </div>
            <div className="w-full flex justify-center items-center flex-col py-24">
                <h1 className="font-bebasNeue md:text-4xl text-blueBlack">Improve your knowledge right now!</h1>
                <div className="w-fit py-4">
                    <Link href={"/signup"}>
                        <Btn button={{
                            text: "Join Now!",
                            type: "button",
                            className: "bg-vibrantCoral text-white"
                        }}
                        /></Link>
                </div>
            </div>
        </div>
    );
};

export default Page;
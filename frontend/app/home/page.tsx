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

    const parallaxSpeed = 0.3;

    return (
        <div className="w-full">
            {/* LANDING SEKCE */}
            <section className="relative w-full h-screen overflow-hidden">
                {/* Obrázky */}
                <div className="absolute top-0 left-0 w-full h-full flex z-0">
                    {/* Levý obrázek */}
                    <div
                        className="relative w-1/3 h-full"
                        style={{
                            transform: `translateY(${scrollY * parallaxSpeed}px)`,
                            transition: "transform 0.1s linear",
                        }}
                    >
                        <img
                            src="/assets/left.jpg"
                            alt="Left"
                            className="w-full h-full object-cover"
                        />
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
                            Training arena for smart bettors
                        </p>
                        <Btn>Join Now!</Btn>
                    </div>
                </div>
            </section>

            {/* DALŠÍ SEKCE */}
            <section className="bg-white text-gray-900 py-20 px-6 md:px-20">
                <h2 className="text-3xl font-semibold mb-6">About us</h2>
                <p className="text-lg leading-relaxed">
                    ---
                </p>
            </section>

            <section className="bg-gray-100 py-20 px-6 md:px-20">
                <h2 className="text-3xl font-semibold mb-6">Our services</h2>
            </section>
        </div>
    );
};

export default Page;
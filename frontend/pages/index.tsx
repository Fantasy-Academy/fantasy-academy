'use client'
import React from "react";
import Btn from "../components/button/Btn";
import Link from "next/link";
import Head from "next/head";

const Page = () => {
    return (
        <div className="w-full">
            <Head>
                <title>Home | Fantasy Academy</title>
            </Head>
            {/* LANDING SEKCE */}
            <section className="relative w-full h-screen overflow-hidden">
                {/* Obrázky */}
                <div className="absolute top-0 left-0 w-full h-full flex z-0">
                    {/* Levý obrázek */}
                    <div className="relative w-1/3 h-full">
                        <img
                            src="/assets/left.jpg"
                            alt="Left"
                            className="w-full h-full object-cover"
                        />
                    </div>

                    {/* Prostřední obrázek */}
                    <div className="w-1/3 h-full">
                        <div className="w-full h-full">
                            <img
                                src="/assets/center.png"
                                alt="center"
                                className="w-full h-full object-cover"
                            />
                        </div>
                    </div>

                    {/* Pravý obrázek */}
                    <div className="relative w-1/3 h-full">
                        <img
                            src="/assets/right.jpg"
                            alt="Right"
                            className="w-full h-full object-cover"
                        />
                    </div>
                </div>

                {/* Text v popředí */}
                <div className="absolute inset-0 flex items-center justify-center text-white text-center px-4 z-10">
                    <div>
                        <h1 className="text-6xl md:text-8xl font-bold mb-6 drop-shadow-lg tracking-wide font-bebasNeue">
                            Fantasy Academy
                        </h1>
                        <p className="text-2xl md:text-4xl mb-8 drop-shadow-md font-caveat">
                            Training arena for smart bettors
                        </p>
                        <Link href="/signup">
                            <Btn>Join Now!</Btn>
                        </Link>
                    </div>
                </div>
            </section>

            {/* DALŠÍ SEKCE */}
            <section className="bg-white text-gray-900 py-20 px-6 md:px-20 flex flex-col gap-4">
                <h2 className="text-3xl font-semibold mb-6">About us</h2>
                <p className="text-lg leading-relaxed">
                    Fantasy Academy is where sports fans turn into masters of strategy. We offer a realistic sports betting simulation that lets you train your prediction skills, test different tactics, and compete against other players – all without any financial risk.
                </p>
                <p className="text-lg leading-relaxed">
                    Fantasy Academy is perfect for beginners looking to understand the principles of betting, as well as experienced players who want to refine their strategies. Here, you can take virtual risks but earn real recognition and respect from the community.
                </p>
            </section>

            <section className="bg-gray-100 py-20 px-6 md:px-20">
                <h2 className="text-3xl font-semibold mb-6">Our Services</h2>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                    <div className="bg-white p-6 rounded-lg shadow-md">
                        <h3 className="text-xl font-bold mb-3">🎯 Sports Betting Simulation</h3>
                        <p className="text-gray-700">
                            Experience the thrill of betting in a completely safe environment. Practice your skills, try different betting markets, and test your strategies – without risking real money.
                        </p>
                    </div>

                    <div className="bg-white p-6 rounded-lg shadow-md">
                        <h3 className="text-xl font-bold mb-3">🏆 Competitive Leaderboards</h3>
                        <p className="text-gray-700">
                            Challenge other players, climb the rankings, and prove you’re the best. Every match and every point counts on your journey to the top.
                        </p>
                    </div>

                    <div className="bg-white p-6 rounded-lg shadow-md">
                        <h3 className="text-xl font-bold mb-3">📊 Performance Tracking</h3>
                        <p className="text-gray-700">
                            Review your bets, analyze your results, and learn from both wins and losses with our easy-to-use tracking tools.
                        </p>
                    </div>

                    <div className="bg-white p-6 rounded-lg shadow-md">
                        <h3 className="text-xl font-bold mb-3">🎮 Seasonal Challenges & Rewards</h3>
                        <p className="text-gray-700">
                            Take part in themed challenges, special events, and seasonal competitions to win exclusive rewards and bragging rights.
                        </p>
                    </div>

                    <div className="bg-white p-6 rounded-lg shadow-md">
                        <h3 className="text-xl font-bold mb-3">🤝 Community Interaction</h3>
                        <p className="text-gray-700">
                            Join a vibrant community of sports fans and strategy lovers. Share tips, discuss matches, and connect with like-minded players worldwide.
                        </p>
                    </div>

                </div>
            </section>
        </div>
    );
};

export default Page;
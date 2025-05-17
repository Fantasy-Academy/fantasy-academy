'use client'
import React from "react";
import Btn from "../../components/button/Btn";
import Link from "next/link";

const Page = () => {
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
                            <Btn>Join Now!</Btn>
                        </Link>
                    </div>
                </div>
            </div>
            <div className="flex flex-row w-full justify-center px-4">
                <div className="w-[500px] h-[200px] p-4 m-3 bg-white rounded-sm relative z-1">
                    <div className="relative">
                        <div className="absolute bg-goldenYellow w-[110px] h-full left-2 top-0 z-0"></div>
                        <h1 className="relative z-10 font-bebasNeue md:text-4xl text-blueBlack px-4">
                            Biggest Community
                        </h1>
                    </div>
                    <p className="font-sourceSans3 text-coolGray mt-2">
                        Lorem impsum dolor, lorem dolor soro impsu. Lorem impsum dolor, lorem dolor soro impsu.
                        Lorem impsum dolor, lorem dolor soro impsu.
                    </p>
                </div>
                <div className="w-[500px] h-[200px] p-4 m-3 bg-white rounded-sm relative z-1">
                    <div className="relative">
                        <div className="absolute bg-goldenYellow w-[110px] h-full left-2 top-0 z-0"></div>
                        <h1 className="relative z-10 font-bebasNeue md:text-4xl text-blueBlack px-4">
                            Daily Repports
                        </h1>
                    </div>
                    <p className="font-sourceSans3 text-coolGray mt-2">
                        Lorem impsum dolor, lorem dolor soro impsu. Lorem impsum dolor, lorem dolor soro impsu.
                        Lorem impsum dolor, lorem dolor soro impsu.
                    </p>
                </div>
                <div className="w-[500px] h-[200px] p-4 m-3 bg-white rounded-sm relative z-1">
                    <div className="relative">
                        <div className="absolute bg-goldenYellow w-[110px] h-full left-2 top-0 z-0"></div>
                        <h1 className="relative z-10 font-bebasNeue md:text-4xl text-blueBlack px-4">
                            Professional Analysis
                        </h1>
                    </div>
                    <p className="font-sourceSans3 text-coolGray mt-2">
                        Lorem impsum dolor, lorem dolor soro impsu. Lorem impsum dolor, lorem dolor soro impsu.
                        Lorem impsum dolor, lorem dolor soro impsu.
                    </p>
                </div>
            </div>
            <div className="w-full flex justify-center items-center flex-col py-24">
                <h1 className="font-bebasNeue md:text-4xl text-blueBlack">Improve your knowledge right now!</h1>
                <div className="w-fit py-4">
                    <Link href={"/signup"}>
                        <Btn>Join Now!</Btn>
                    </Link>
                </div>
            </div>
        </div>
    );
}

export default Page;
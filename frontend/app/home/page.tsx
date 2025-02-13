'use client'
import React from "react";
import Btn from "../../components/button/Btn";
import Link from "next/link";

const Page = () => {
    return (
        <div>
            <h1 className="font-bebasNeue text-9xl text-blueBlack">TRAIN LIKE A PRO</h1>
            <h3 className="font-caveat text-5xl text-blueBlack">Training arena for smart bettors.</h3>
            <div className="w-fit">
                <Link href={"/signup"}>
                    <Btn text="Join Now!" className="bg-goldenYellow text-blueBlack px-6 py-4 text-xl" />
                </Link>
            </div>
        </div>
    );
}

export default Page;
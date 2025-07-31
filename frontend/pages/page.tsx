import React from "react";
import { redirect } from 'next/navigation';


export default function Page() {
  redirect('/dashboard');

  return (
    <div>
      <h1 className="text-3xl font-bold">Home</h1>
    </div>
  );
}

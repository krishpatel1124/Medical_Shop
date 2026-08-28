const http=require("http");
const fs=require("fs");
const path=require("path");
const PORT=2000;
http.createServer((req,res)=>{
    const pathfile=path.join(__dirname,"public",req.url==="\/"? "show.html":req.url);
    fs.readFile((err,data)=>{
        if(err)
        {
            res.writeHead(404);
            res.end("File not found");
        }
        else{
            res.writeHead(200);
            res.end(data);
        }

    });
}).listen(PORT,()=>{console.log("server is running on http://localhost:"+PORT)});
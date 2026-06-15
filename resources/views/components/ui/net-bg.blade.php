{{-- Ambient glow blobs --}}
<div style="position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden;">
    <div style="position:absolute;width:600px;height:600px;top:-120px;left:-100px;background:radial-gradient(circle,rgba(94,106,210,0.18) 0%,transparent 70%);border-radius:50%;"></div>
    <div style="position:absolute;width:500px;height:500px;bottom:-80px;right:-80px;background:radial-gradient(circle,rgba(129,140,248,0.14) 0%,transparent 70%);border-radius:50%;"></div>
    <div style="position:absolute;width:400px;height:400px;top:40%;left:50%;transform:translate(-50%,-50%);background:radial-gradient(circle,rgba(34,211,238,0.06) 0%,transparent 70%);border-radius:50%;"></div>
</div>
<canvas id="net-bg" style="position:fixed;inset:0;width:100%;height:100%;z-index:0;pointer-events:none;"></canvas>
<script>
(function(){
    var c=document.getElementById('net-bg'),ctx=c.getContext('2d');
    var W,H,pts=[],N=90,MAX_D=160,SPEED=0.28;
    var COLORS=['94,106,210','129,140,248','34,211,238','99,102,241'];
    function resize(){W=c.width=window.innerWidth;H=c.height=window.innerHeight;}
    resize();window.addEventListener('resize',function(){resize();pts=mk();});
    function rnd(a,b){return a+Math.random()*(b-a);}
    function mk(){var a=[];for(var i=0;i<N;i++)a.push({x:rnd(0,W),y:rnd(0,H),vx:rnd(-SPEED,SPEED)||SPEED,vy:rnd(-SPEED,SPEED)||SPEED,col:COLORS[Math.floor(Math.random()*COLORS.length)]});return a;}
    pts=mk();
    function draw(){
        ctx.clearRect(0,0,W,H);
        for(var i=0;i<N;i++){var p=pts[i];p.x+=p.vx;p.y+=p.vy;if(p.x<0||p.x>W)p.vx*=-1;if(p.y<0||p.y>H)p.vy*=-1;}
        for(var i=0;i<N;i++){
            for(var j=i+1;j<N;j++){
                var dx=pts[i].x-pts[j].x,dy=pts[i].y-pts[j].y,d=Math.sqrt(dx*dx+dy*dy);
                if(d<MAX_D){var a=(1-d/MAX_D)*0.32;ctx.beginPath();ctx.moveTo(pts[i].x,pts[i].y);ctx.lineTo(pts[j].x,pts[j].y);ctx.strokeStyle='rgba('+pts[i].col+','+a+')';ctx.lineWidth=0.7;ctx.stroke();}
            }
        }
        for(var i=0;i<N;i++){ctx.beginPath();ctx.arc(pts[i].x,pts[i].y,1.5,0,6.28);ctx.fillStyle='rgba('+pts[i].col+',0.55)';ctx.fill();}
        requestAnimationFrame(draw);
    }
    draw();
})();
</script>

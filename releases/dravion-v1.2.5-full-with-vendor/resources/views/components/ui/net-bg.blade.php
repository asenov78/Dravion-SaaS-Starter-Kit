<canvas id="net-bg" style="position:fixed;inset:0;width:100%;height:100%;z-index:0;pointer-events:none;"></canvas>
<script>
(function(){
    var c=document.getElementById('net-bg'),ctx=c.getContext('2d');
    var W,H,pts=[],N=55,MAX_D=155,SPEED=0.25;
    function resize(){W=c.width=window.innerWidth;H=c.height=window.innerHeight;}
    resize();window.addEventListener('resize',function(){resize();pts=mk();});
    function rnd(a,b){return a+Math.random()*(b-a);}
    function mk(){var a=[];for(var i=0;i<N;i++)a.push({x:rnd(0,W),y:rnd(0,H),vx:rnd(-SPEED,SPEED)||SPEED,vy:rnd(-SPEED,SPEED)||SPEED});return a;}
    pts=mk();
    function draw(){
        ctx.clearRect(0,0,W,H);
        for(var i=0;i<N;i++){var p=pts[i];p.x+=p.vx;p.y+=p.vy;if(p.x<0||p.x>W)p.vx*=-1;if(p.y<0||p.y>H)p.vy*=-1;}
        for(var i=0;i<N;i++){
            for(var j=i+1;j<N;j++){
                var dx=pts[i].x-pts[j].x,dy=pts[i].y-pts[j].y,d=Math.sqrt(dx*dx+dy*dy);
                if(d<MAX_D){var a=(1-d/MAX_D)*0.13;ctx.beginPath();ctx.moveTo(pts[i].x,pts[i].y);ctx.lineTo(pts[j].x,pts[j].y);ctx.strokeStyle='rgba(34,211,238,'+a+')';ctx.lineWidth=0.6;ctx.stroke();}
            }
        }
        for(var i=0;i<N;i++){ctx.beginPath();ctx.arc(pts[i].x,pts[i].y,1.3,0,6.28);ctx.fillStyle='rgba(34,211,238,0.28)';ctx.fill();}
        requestAnimationFrame(draw);
    }
    draw();
})();
</script>

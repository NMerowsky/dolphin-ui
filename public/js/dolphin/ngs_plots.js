function draw(data){ 
  "use strict";

  var w=800, h=400;
  var pad = {left: 20, right: 20, top: 20, bottom: 20};

  var svg = d3.select('#plots_exp_body')
                .append("svg")
                  .attr("height", h)
                  .attr("width", w);

  svg.append("rect")
       .attr("control_rep1", pad.left).attr("control_rep2",pad.top)
       .attr("width", w-(pad.left+pad.right))
       .attr("height",h-(pad.top+pad.bottom))
       .attr("stroke", "black")
       .attr("fill", "rgb(230,230,230)");

  var xMin = d3.min(data, function(d) { return Number(d.control_rep1); });
  var xMax = d3.max(data, function(d) { return Number(d.control_rep1); });
  var yMin = d3.min(data, function(d) { return Number(d.control_rep2); });
  var yMax = d3.max(data, function(d) { return Number(d.control_rep2); });

  var xScale = d3.scale.log().domain([0.1, xMax]).range([pad.left*2, w-pad.right*2]);
  var yScale = d3.scale.log().domain([0.1, yMax]).range([h-pad.bottom*2, pad.top*2]);

  var circles = svg.selectAll("circle")
                      .data(data)
                      .enter()
                        .append("circle")
                            .attr("cx", function(d) { return xScale(d.control_rep1); })
                            .attr("cy", function(d) { return yScale(d.control_rep2); })
                            .attr("r", 5)
                            .attr("opacity", 0.8)
                            .attr("fill", function(d) { return ['blue', 'green', 'red'][1]; })
                            .on("mouseover", function() { d3.select(this).attr("r", 10) })
                            .on("mouseout", function() { d3.select(this).attr("r", 5) });
                            
    //Make sure to disable loading screen
    document.getElementById('overlay').parentNode.removeChild( document.getElementById('overlay'));
}
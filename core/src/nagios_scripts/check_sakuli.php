<?php
# PNP template for Sakuli checks 
# Copyright (C) 2015 The Sakuli Team, <sakuli@consol.de>
# See https://github.com/ConSol/sakuli for more information. 

#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.



isset($_GET['debug']) ? $DEBUG = $_GET['debug'] : $DEBUG = 0;
$debug_log = "/tmp/pnp_check_sakuli.php.log";

# Position vars
$perf_pos_suite_state = 1;
$perf_pos_suite_runtime = 3;

$col_invisible = '#00000000';

# Colors for the suite/case runtime overhead over cases/steps
$col_suite_runtime_line = '#31a354';
$col_suite_runtime_area = '#a1d99b';
$col_case_runtime_line = $col_suite_runtime_line;
$col_case_runtime_area = $col_suite_runtime_area;

# Case colors
$col_case_line = $this->config->scheme['Blues'];
$col_case_line = array_merge($col_case_line, $col_case_line, $col_case_line, $col_case_line);
$col_case_area = $col_case_line;
$col_case_area_opacity = "BB";

# Step colors

$col_step_line = $this->config->scheme['Spectral'];
$col_step_line = array_merge($col_step_line, $col_step_line, $col_step_line, $col_step_line);
$col_step_area = $col_step_line;
$col_step_area_opacity = "BB";

# State colors
$col_OK = "#008500";
$col_warning = "#ffdd00";
$col_critical = "#ff0000";
$col_INCOMPL = "#ff8000"; # orange

# CPU Usage color
$col_cpu = "#ffff0099";

# Memory Usage color
$col_mem = "#00b30099";

# TICK line (incomplete data / warning / critical) 
$tick_dist_factor = "1.05";
$tick_frac = "-0.03";
#$tick_opacity_incompl = "44";
$tick_opacity_warn = "AA";
$tick_opacity_crit = "AA";
$tick_opacity_incompl = "AA";

sort($this->DS);

$suitename = preg_replace('/^suite_(.*)$/', '$1', $NAME[$perf_pos_suite_runtime]);

# Determine length of all labels
$label_max_length = 0;
$labels = array();

# Loop over case names
foreach($this->DS as $k=>$v) {
        if (preg_match('/(c|s)_(\d+)_(\d+_)?([a-zA-Z0-9].*)/', $v["LABEL"], $matches)) {
		array_push($labels, strlen($matches[4]));
	}
}
array_push($labels, strlen($suitename));
$label_max_length = max($labels);

#   ____ ____  _   _       __  __ _____ __  __ 
#  / ___|  _ \| | | |  _  |  \/  | ____|  \/  |
# | |   | |_) | | | |_| |_| |\/| |  _| | |\/| |
# | |___|  __/| |_| |_   _| |  | | |___| |  | |
#  \____|_|    \___/  |_| |_|  |_|_____|_|  |_|
                                             
# show CPU/MEM graphs only if Macros are set properly. For more information, see
# https://github.com/ConSol/sakuli/blob/master/docs/installation-omd.md#include-cpumem-graphs-in-sakuli-graphs-optional
if ( ( (array_key_exists('E2ECPUHOST', $this->MACRO)) and ($this->MACRO['E2ECPUHOST'] != '$_HOSTE2E_CPU_HOST$')) and ( ((array_key_exists('E2ECPUSVC', $this->MACRO))) and ($this->MACRO['E2ECPUSVC'] != '$_HOSTE2E_CPU_SVC$'))) {
	if (preg_match('/usage/i', $this->MACRO['E2ECPUSVC'])) {
        	$graph_cpu = "%";
	        $rrddef_cpu = rrd::def("cpu_usage", OMD_SITE_ROOT . "/var/pnp4nagios/perfdata/" .
	                $this->MACRO['E2ECPUHOST'] . "/" .
	                $this->MACRO['E2ECPUSVC'] . ".rrd",1,"AVERAGE");
	        $rrddef_cpu .= rrd::line1("cpu_usage", $col_cpu, pad("CPU Usage", $label_max_length));
	        $rrddef_cpu .= rrd::gprint("cpu_usage", "MAX", "%3.2lf%%  MAX ");
	        $rrddef_cpu .= rrd::gprint("cpu_usage", "AVERAGE", "%3.2lf%%  AVG ");
	        $rrddef_cpu .= rrd::gprint("cpu_usage", "LAST", "%3.2lf%%  LAST \j");
	} else if (preg_match('/load/i', $this->MACRO['E2ECPUSVC'])) {
        	$graph_cpu = "load";
                $rrddef_cpu = rrd::def("cpu_load", OMD_SITE_ROOT . "/var/pnp4nagios/perfdata/" .
                        $this->MACRO['E2ECPUHOST'] . "/" .
                        $this->MACRO['E2ECPUSVC'] . ".rrd",1,"AVERAGE");
		# Load is usually a much lower value than usage (%) -> multiply by 10 and scale right axis
		$rrddef_cpu .= rrd::cdef("cpu_load10", "cpu_load,10,*");
                $rrddef_cpu .= rrd::line1("cpu_load10", $col_cpu, pad("CPU Load", $label_max_length));
                $rrddef_cpu .= rrd::gprint("cpu_load", "MAX", "%3.2lf MAX ");
                $rrddef_cpu .= rrd::gprint("cpu_load", "AVERAGE", "%3.2lf AVG ");
                $rrddef_cpu .= rrd::gprint("cpu_load", "LAST", "%3.2lf LAST \j");
	}
} else {
        $graph_cpu = false;
        $rrdopts_cpu = "";
}
if ( ( (array_key_exists('E2EMEMHOST', $this->MACRO)) and ($this->MACRO['E2EMEMHOST'] != '$_HOSTE2E_MEM_HOST$')) and ( ((array_key_exists('E2EMEMSVC', $this->MACRO))) and ($this->MACRO['E2EMEMSVC'] != '$_HOSTE2E_MEM_SVC$'))) {
        $graph_mem = true;
        $rrddef_mem = rrd::def("mem_usage", OMD_SITE_ROOT ."/var/pnp4nagios/perfdata/" .
                $this->MACRO['E2EMEMHOST'] . "/" .
                $this->MACRO['E2EMEMSVC'] . "_physical_memory_%.rrd",1,"AVERAGE");
        $rrddef_mem .= rrd::line1("mem_usage", $col_mem, "phys. Memory Usage");
        $rrddef_mem .= rrd::gprint("mem_usage", "MAX", "%3.2lf%% MAX ");
        $rrddef_mem .= rrd::gprint("mem_usage", "AVERAGE", "%3.2lf%% AVG ");
        $rrddef_mem .= rrd::gprint("mem_usage", "LAST", "%3.2lf%% LAST \j");
} else {
        $graph_mem = false;
        $rrdopts_mem = "";
}

#            _ _                               _     
#  ___ _   _(_) |_ ___    __ _ _ __ __ _ _ __ | |__  
# / __| | | | | __/ _ \  / _` | '__/ _` | '_ \| '_ \ 
# \__ \ |_| | | ||  __/ | (_| | | | (_| | |_) | | | |
# |___/\__,_|_|\__\___|  \__, |_|  \__,_| .__/|_| |_|
#                       |___/          |_|          

$ds_name[0] = "Sakuli Suite '" . $suitename . "'";
$opt[0] = "--vertical-label \"seconds\"  -l 0 --slope-mode --title \"$servicedesc (Sakuli Suite $suitename) on $hostname\" ";
$def[0] = "";

# Suite graph, Case AREA ##########################################
foreach($this->DS as $k=>$v) {
	# c_001_case1
	# but do not match a _state_ label like 'c_001__state_demo_win7' (which contains two underscores)
	if (preg_match('/c_(\d+)_([a-zA-Z0-9].*)/', $v["LABEL"], $c_matches)) {
		$casecount = $c_matches[1];
		$casecount_int = intval($casecount);
		$casename = $c_matches[2];
		$def[0] .= rrd::def("c_area$casecount", $v["RRDFILE"], $v["DS"], "AVERAGE");
		if ($casecount == "001") {
			$def[0] .= rrd::comment("Sakuli Cases\: \\n");
			$def[0] .= rrd::cdef("c_area_stackbase$casecount", "c_area$casecount,1,*");
			$def[0] .= rrd::area("c_area$casecount", $col_case_area[$casecount_int].$col_case_area_opacity, pad($casename, $label_max_length), 0);
		} else {
			# all areas >1 are stacked upon a invisible line 
			$def[0] .= rrd::line1("c_area_stackbase".lead3($casecount_int-1),"#00000000");
			$def[0] .= rrd::area("c_area$casecount", $col_case_area[$casecount_int].$col_case_area_opacity, $casename, 1);
			# add value to stackbase
			$def[0] .= rrd::cdef("c_area_stackbase$casecount", "c_area_stackbase".lead3($casecount_int-1).",c_area$casecount,+");
		}

		$def[0] .= rrd::gprint("c_area$casecount", "LAST", "%3.2lf s LAST");
		$def[0] .= rrd::gprint("c_area$casecount", "MAX", "%3.2lf s MAX ");
		$def[0] .= rrd::gprint("c_area$casecount", "AVERAGE", "%3.2lf s AVG \j");

	}
}

# Suite graph, Case LINE ##########################################

$c_last_index = "";
foreach($this->DS as $k=>$v) {
	# c_001_case1
	# do not match a state label like 'c_1__state_demo_win7' (which contains two underscores)
	if (preg_match('/c_(\d+)_([a-zA-Z0-9].*)/', $v["LABEL"], $c_matches)) {
		$casecount = $c_matches[1];
		$casecount_int = intval($casecount);
		$casename = $c_matches[2];
		$def[0] .= rrd::def("c_line$casecount", $v["RRDFILE"], $v["DS"], "AVERAGE");
		if ($casecount == "001") {
			$def[0] .= rrd::cdef("c_line_stackbase$casecount", "c_line$casecount,1,*");
			$def[0] .= rrd::line1("c_line$casecount", $col_case_line[$casecount_int], "", 0);
		} else {
			# invisible line to stack upon
			$def[0] .= rrd::line1("c_line_stackbase".lead3($casecount_int-1),"#00000000");
			$def[0] .= rrd::line1("c_line$casecount", $col_case_area[$casecount_int], "", 1);
			# add value to stackbase
			$def[0] .= rrd::cdef("c_line_stackbase$casecount", "c_line_stackbase".lead3($casecount_int-1).",c_line$casecount,+");
		}

		# remember the last index
		$c_last_index = $casecount;
	}
}	

# Suite Legend ########################################
$def[0] .= rrd::comment(" \\n");
$def[0] .= rrd::comment("Sakuli Suite\g");

foreach($this->DS as $k=>$v) {
	$before = "  (";
	if (preg_match('/^suite__(warning|critical)/', $v['LABEL'], $matches)) {
		$threshold = $matches[1];
		if ($before == "") {
			$before = ", "; 
		}
		$def[0] .= rrd::def("suite_".$threshold, $v["RRDFILE"], $v["DS"], "AVERAGE");
		$def[0] .= rrd::comment($before .  "\g");
		$def[0] .= rrd::line1("suite_".$threshold, ${"col_" . $threshold}, $threshold . "\g", 0);
		$def[0] .= rrd::gprint("suite_".$threshold, "LAST", "%3.0lf s \g" );
		$before = ""; 
	}
	if ($before == ""){
		$def[0] .= rrd::comment(")\g");
	}
}

# Suite Graph, Suite runtime on top ################################
$def[0] .= rrd::comment("\:\\n");
$def[0] .= rrd::def("suite", $RRDFILE[$perf_pos_suite_runtime], $DS[$perf_pos_suite_runtime], "AVERAGE");
if ($c_last_index != "") {
	$def[0] .= rrd::cdef("suite_diff", "suite,c_line_stackbase".$c_last_index.",UN,0,c_line_stackbase".$c_last_index.",IF,-");
	# invisible line to stack upon
	$def[0] .= rrd::line1("c_line_stackbase".($c_last_index),"#00000000");
	$def[0] .= rrd::area("suite_diff", $col_suite_runtime_area,pad($suitename, $label_max_length),1 );
	# invisible line to stack upon
	$def[0] .= rrd::line1("c_line_stackbase".($c_last_index),"#00000000");
	$def[0] .= rrd::line1("suite_diff", $col_suite_runtime_line, "",1 );
} else {
	# no cases, no STACKing
	$def[0] .= rrd::area("suite", $col_suite_runtime_area,$suitename );
	$def[0] .= rrd::line1("suite", $col_suite_runtime_line, "" );
}

# Suite Legend #########################################
$def[0] .= rrd::gprint("suite", "LAST", "%3.2lf ".$UNIT[$perf_pos_suite_runtime]." LAST");
$def[0] .= rrd::gprint("suite", "MAX", "%3.2lf ".$UNIT[$perf_pos_suite_runtime]." MAX ");
$def[0] .= rrd::gprint("suite", "AVERAGE", "%3.2lf ".$UNIT[$perf_pos_suite_runtime]." AVG \j");
$def[0] .= rrd::comment(" \\n");

# invisible line above maximum (for space between MAX and TICKER) ############################	
$def[0] .= rrd::def("suite_max", $RRDFILE[$perf_pos_suite_runtime], $DS[$perf_pos_suite_runtime], "MAX") ;
$def[0] .= rrd::cdef("suite_maxplus", "suite_max,".$tick_dist_factor.",*");
$def[0] .= rrd::line1("suite_maxplus", $col_invisible);
 
########################
# TICKS for suite state (jumpmark for case: 1f183) 
########################


$def[0] .= rrd::comment("State ticker legend\:\g");
$def[0] .= rrd::comment(" \\n");

# TICKS for incomplete data ("data" = case/suite LINE) ######################################################
# - complete data   -->  suite_unknown_total == 0 --> no TICK (resp. warn/crit TICK) 
# - incomplete data -->  0 < suite_unknown_total < suite_data_count --> orange TICK (overwrites evt warn/crit TICKS) 
# - no data at all  -->  suite_unknown_total == suite_data_count --> no TICK

# Argh. cdefs without a variable are not possible.
# "suite,suite,-" to generate 0 is no solution, because the new cdef var still has unknown values where suite had one. 
# Hence, simply replace all UN values with 0

# RRDtool does not allow to increment a CDEF var multiple times, as needed here. $suite_data_count is a counter for 
# each data row which lets us create a new CDEF each time and use the last one for graphing. 
$def[0] .= rrd::cdef("ts_suite_unknown_total_0", "suite,UN,0,0,IF");
$suite_data_count = 0;

# 1. case runtimes unknown?
foreach ($this->DS as $k=>$v) {
	if (preg_match('/c_(\d+)_([a-zA-Z0-9].*)/', $v["LABEL"], $c_matches)) {
		$suite_data_count++;
		$case_no = $c_matches[1];
		# flag to 1 if UN
		$def[0] .= rrd::cdef("ts_case_".$case_no."_unknown", "c_line$case_no,UN,1,0,IF");
		# create new unknown_total cdef, add flag (0/1) to unknown_total_XXXX-1
		$def[0] .= rrd::cdef("ts_suite_unknown_total_".$suite_data_count,"ts_suite_unknown_total_".($suite_data_count-1).",ts_case_".$case_no."_unknown,+");
	}
}

# 2. suite runtime unknown? 
$suite_data_count++;
# flag to 1 if UN
$def[0] .= rrd::cdef("ts_suite_unknown", "suite,UN,1,0,IF");
# create new unknown_total cdef, add flag (0/1) to unknown_total_XXXX-1
$def[0] .= rrd::cdef("ts_suite_unknown_total_".$suite_data_count, "ts_suite_unknown_total_".($suite_data_count-1).",ts_suite_unknown,+");

# 3. calculate
# break the comparison 0 < x < y into two, each results in either 0 (false) or 1 (true) 
$def[0] .= rrd::cdef("ts_suite_incomplete_gt0", "ts_suite_unknown_total_".$suite_data_count.",0,GT,1,0,IF");
$def[0] .= rrd::cdef("ts_suite_incomplete_ltdatacount", "ts_suite_unknown_total_".$suite_data_count.",".$suite_data_count.",LT,1,0,IF");
# if the sum of both comparisons is 2, both conditions are true and we really have incomplete data. This is when we want to have the TICK.
$def[0] .= rrd::cdef("ts_suite_incomplete", "ts_suite_incomplete_gt0,ts_suite_incomplete_ltdatacount,+,2,EQ,1,0,IF");
$def[0] .= "TICK:ts_suite_incomplete".$col_INCOMPL.$tick_opacity_incompl.":".$tick_frac.":'incomplete run' " ;

# TICKS for warning/critical -> yellow/red  ##############################################
foreach ($this->DS as $k=>$v) {
	if (preg_match('/suite__state/', $v["LABEL"], $c_matches)) {
		$def[0] .= rrd::def("ts_suite_state", $v['RRDFILE'], $v['DS'], "MAX") ;

		# determine when this suite was warning/critical -> draw TICK line on top
		$def[0] .= rrd::cdef("ts_suite_state_warning", "ts_suite_state,1,EQ,1,0,IF ") ;
		# if incomplete data = 1 -> 0 = no crit TICK
		# else: if suite_state = 2 -> 1 = crit TICK
		$def[0] .= rrd::cdef("ts_suite_state_critical", "ts_suite_incomplete,1,EQ,0,ts_suite_state,2,EQ,1,0,IF,IF") ;

		$def[0] .= "TICK:ts_suite_state_warning".$col_warning.$tick_opacity_warn.":".$tick_frac.":'case/suite warning ' " ;
		$def[0] .= "TICK:ts_suite_state_critical".$col_critical.$tick_opacity_crit.":".$tick_frac.":'case/suite critical ' " ;
	}
}


# Suite last check time: black vertical line ##############################################
$def[0] .= "VRULE:".$NAGIOS_TIMET."#000000: ";

# append CPU/MEM graph (defined above) ################################################
if ($graph_cpu or $graph_mem) {
	$def[0] .= rrd::comment(" \\n");
	$def[0] .= rrd::comment("Host Statistics\:\\n");
	if ($graph_cpu == "load" ) {
		# Load is usually a much lower value than usage (%) -> scale the right axis with factor 10
		$opt[0] .= " --right-axis \"0.1:0\" --right-axis-label \"CPU Load\" ";
	} else {
		$opt[0] .= " --right-axis \"1:0\" --right-axis-label \"CPU Usage\" ";
	}
}
if ( $graph_cpu ) {
	$def[0] .= $rrddef_cpu;	
}
if ( $graph_mem ) {
	$def[0] .= $rrddef_mem;	
}

#                                            _     
#   ___ __ _ ___  ___    __ _ _ __ __ _ _ __ | |__  
#  / __/ _` / __|/ _ \  / _` | '__/ _` | '_ \| '_ \ 
# | (_| (_| \__ \  __/ | (_| | | | (_| | |_) | | | |
#  \___\__,_|___/\___|  \__, |_|  \__,_| .__/|_| |_|
#                       |___/          |_|          

foreach ($this->DS as $KEY=>$VAL) {
	# c_001_case1
	if (preg_match('/^c_(\d+)_([a-zA-Z0-9].*)/', $VAL['LABEL'], $c_matches)) {
		$casecount = $c_matches[1];
		$casecount_int = intval($casecount);
		$casename = $c_matches[2];
		$ds_name[$casecount_int] = "Sakuli Case $casename";
		$opt[$casecount_int] = "--vertical-label \"seconds\"  -l 0 -M --slope-mode --title \"$servicedesc (Sakuli case $casename) on $hostname\" ";
		$def[$casecount_int] = "";
		# Case graph, step AREA ########################################
		foreach ($this->DS as $k=>$v) {
			# s_001_001_stepone
			# s_001_002_steptwo
			# ...
			if (preg_match('/^s_'.$casecount.'_(\d+)_(.*)/', $v['LABEL'], $s_matches)) {
				$stepcount = $s_matches[1];
				$stepcount_int = intval($stepcount);
				$stepname = $s_matches[2];
				$def[$casecount_int] .= rrd::def("s_area$stepcount", $v['RRDFILE'], $v['DS'], "AVERAGE");

				if ($stepcount == "001"){
					# first step
					$def[$casecount_int] .= rrd::comment("Steps\: \\n");
					$def[$casecount_int] .= rrd::cdef("s_area_stackbase$stepcount", "s_area$stepcount,1,*");
	        			$def[$casecount_int] .= rrd::area("s_area$stepcount", $col_step_area[$stepcount_int].$col_step_area_opacity,pad($stepname, $label_max_length), 0 );
				} else {
					# all areas >1 are stacked upon a invisible line 
					$def[$casecount_int] .= rrd::line1("s_area_stackbase" . lead3($stepcount - 1), "#00000000");
					$def[$casecount_int] .= rrd::area("s_area$stepcount", $col_step_area[$stepcount_int].$col_step_area_opacity,pad($stepname, $label_max_length), 1 );
					# add value to s_area_stackbase
					$def[$casecount_int] .= rrd::cdef("s_area_stackbase$stepcount", "s_area_stackbase".lead3($stepcount_int-1).",s_area$stepcount,+");
				}
				$def[$casecount_int] .= rrd::gprint("s_area$stepcount", "LAST", "%3.2lf s LAST");
				$def[$casecount_int] .= rrd::gprint("s_area$stepcount", "MAX", "%3.2lf s MAX ");
				$def[$casecount_int] .= rrd::gprint("s_area$stepcount", "AVERAGE", "%3.2lf s AVG \j");
			}
		}
		# invisible line above maximum (for space between MAX and TICKER) ---------------	
		$def[$casecount_int] .= rrd::def("case".$casecount."_max", $VAL['RRDFILE'], $VAL['DS'], "MAX") ;
		$def[$casecount_int] .= rrd::cdef("case".$casecount."_maxplus", "case".$casecount."_max,".$tick_dist_factor.",*");
		$def[$casecount_int] .= rrd::line1("case".$casecount."_maxplus", $col_invisible);

		# Case graph, step LINE #########################################################
		$s_last_index = "";
		foreach ($this->DS as $k=>$v) {
			# s_001_001_stepone
                        # s_001_002_steptwo
                        # ...
			if (preg_match('/^s_'.$casecount.'_(\d+)_(.*)/', $v['LABEL'], $s_matches)) {
				$stepcount = $s_matches[1];
				$stepcount_int = intval($stepcount);
				$stepname = $s_matches[2];
				$def[$casecount_int] .= rrd::def("s_line$stepcount", $v['RRDFILE'], $v['DS'], "AVERAGE");
				if ($stepcount == "001"){
					$def[$casecount_int] .= rrd::cdef("s_line_stackbase$stepcount", "s_line$stepcount,1,*");
					$def[$casecount_int] .= rrd::line1("s_line$stepcount", $col_step_line[$stepcount_int], "", 0 );
				} else {
					# invisible line to stack upon
					$def[$casecount_int] .= rrd::line1("s_line_stackbase".lead3($stepcount_int-1),"#00000000");	
					$def[$casecount_int] .= rrd::line1("s_line$stepcount", $col_step_line[$stepcount_int], "", 1 );
					# add value to s_line_stackbase
					$def[$casecount_int] .= rrd::cdef("s_line_stackbase$stepcount", "s_line_stackbase".lead3($stepcount_int-1).",s_line$stepcount,+");
				}
				# remember the last index
				$s_last_index = $stepcount;
			}
		}

               $def[$casecount_int] .= rrd::comment(" \\n");                                                                        
               $def[$casecount_int] .= rrd::comment("Case ".$casecount_int ."\g");


		# Case graph, Warn/Crit LINE  ##########################################################
		foreach($this->DS as $k=>$v) {
			$before = "  (";
			if (preg_match('/^c_'.$casecount.'__(warning|critical)/', $v['LABEL'], $matches)) {
				$threshold = $matches[1];
				if ($before === "") {
					$before = ", ";
				}
				$def[$casecount_int] .= rrd::def("case_".$casecount . "__" . $threshold, $v["RRDFILE"], $v["DS"], "AVERAGE");
				$def[$casecount_int] .= rrd::comment($before .  "\g");
				$def[$casecount_int] .= rrd::line1("case_".$casecount . "__" . $threshold, ${"col_" . $threshold}, $threshold . "\g", 0);
				$def[$casecount_int] .= rrd::gprint("case_".$casecount . "__" . $threshold, "LAST", "%3.0lf s \g" );
				$before = "";
			}
			if ($before === ""){
				$def[$casecount_int] .= rrd::comment(")\g");
			}
		}



		# Case graph, Case runtime on top ###########################################################
		$def[$casecount_int] .= rrd::comment("\:\\n");
	        $def[$casecount_int] .= rrd::def("case$casecount", $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
		# not used anymore; formerly used to draw grey TICK-areas if any case is unknown to hide everything. 
		$def[$casecount_int] .= rrd::cdef("case".$casecount."_unknown", "case$casecount,UN,1,0,IF");
		if ($s_last_index != "") {
			$def[$casecount_int] .= rrd::cdef("case_diff$casecount","case$casecount,s_line_stackbase$s_last_index,-");
			# invisible line to stack upon
			$def[$casecount_int] .= rrd::line1("s_line_stackbase$s_last_index", "#00000000");
			$def[$casecount_int] .= rrd::area   ("case_diff$casecount", $col_case_runtime_area, pad($casename,$label_max_length),1 );
			# invisible line to stack upon
			$def[$casecount_int] .= rrd::line1("s_line_stackbase$s_last_index","#00000000");	
			$def[$casecount_int] .= rrd::line1   ("case_diff$casecount", $col_case_runtime_line,"",1);
		} else {
			# no steps, no stacks
			$def[$casecount_int] .= rrd::area   ("case$casecount", $col_case_runtime_area, $casename );
			$def[$casecount_int] .= rrd::line1   ("case$casecount", $col_case_runtime_line,"");
		}
		# Case graph, legend #############################################
		$def[$casecount_int] .= rrd::gprint ("case$casecount", "LAST", "%3.2lf s LAST");
		$def[$casecount_int] .= rrd::gprint ("case$casecount", "MAX", "%3.2lf s MAX ");
		$def[$casecount_int] .= rrd::gprint ("case$casecount", "AVERAGE", "%3.2lf s AVG \j");
		$def[$casecount_int] .= rrd::comment(" \\n");

		#######################
		# TICKS for case state (jumpmark for suite: 1f183)
		#######################

		$def[$casecount_int] .= rrd::comment("State ticker legend\:\g");
		$def[$casecount_int] .= rrd::comment(" \\n");


		# TICKS for incomplete data ("data" = step/case LINE) ######################################################
		# - complete data   -->  case_unknown_total == 0 --> no TICK (resp. warn/crit TICK)
		# - incomplete data -->  0 < case_unknown_total < case_data_count --> orange TICK (overwrites evt warn/crit TICKS)
		# - no data at all  -->  case_unknown_total == case_data_count --> no TICK

		# see comments in case tick

		$def[$casecount_int] .= rrd::cdef("tc_case".$casecount."_unknown_total_0", "case".$casecount.",UN,0,0,IF");
		$case_data_count = 0;

		# 1. step runtimes unknown?
		foreach ($this->DS as $k=>$v) {
			if (preg_match('/^s_'.$casecount.'_(\d+)_(.*)/', $v['LABEL'], $s_matches)) {
				$case_data_count++;
				$step_no = $s_matches[1];
				# flag to 1 if UN
				$def[$casecount_int] .= rrd::cdef("tc_step_".$step_no."_unknown", "s_line$step_no,UN,1,0,IF");
				# create new unknown_total cdef, add flag (0/1) to unknown_total_XXXX-1
				$def[$casecount_int] .= rrd::cdef("tc_case".$casecount."_unknown_total_".$case_data_count,"tc_case".$casecount."_unknown_total_".($case_data_count-1).",tc_step_".$step_no."_unknown,+");
			}
		}

		# 2. case runtime unknown? 
		$case_data_count++;
		# flag to 1 if UN
		$def[$casecount_int] .= rrd::cdef("tc_case".$casecount."_unknown", "case$casecount,UN,1,0,IF");
		# create new unknown_total cdef, add flag (0/1) to unknown_total_XXXX-1
		$def[$casecount_int] .= rrd::cdef("tc_case".$casecount."_unknown_total_".$case_data_count, "tc_case".$casecount."_unknown_total_".($case_data_count-1).",tc_case".$casecount."_unknown,+");

		# 3. calculate
		# break the comparison 0 < x < y into two, each results in either 0 (false) or 1 (true) 
		$def[$casecount_int] .= rrd::cdef("tc_case".$casecount."_incomplete_gt0", "tc_case".$casecount."_unknown_total_".$case_data_count.",0,GT,1,0,IF");
		$def[$casecount_int] .= rrd::cdef("tc_case".$casecount."_incomplete_ltdatacount", "tc_case".$casecount."_unknown_total_".$case_data_count.",".$case_data_count.",LT,1,0,IF");
		# if the sum of both comparisons is 2, both conditions are true and we really have incomplete data. This is when we want to have the TICK.
		$def[$casecount_int] .= rrd::cdef("tc_case".$casecount."_incomplete", "tc_case".$casecount."_incomplete_gt0,tc_case".$casecount."_incomplete_ltdatacount,+,2,EQ,1,0,IF");
		$def[$casecount_int] .= "TICK:tc_case".$casecount."_incomplete".$col_INCOMPL.$tick_opacity_incompl.":".$tick_frac.":'incomplete run' " ;

		# TICKS for warning/critical -> yellow/red  ##############################################
		# - warning/critical -> yellow/red TICK line on top
		foreach ($this->DS as $k=>$v) {
			# matches only once = for c_$casecount__state
			if(preg_match('/^c_'.$casecount.'__state/', $v['LABEL'], $state_matches)) {
				$def[$casecount_int] .= rrd::def("tc_case".$casecount."_state", $v['RRDFILE'], $v['DS'], "MAX") ;
	
				# determine when this case is warning/critical -> draw TICK line on top	
				$def[$casecount_int] .= rrd::cdef("tc_case".$casecount."_state_warning", "tc_case".$casecount."_state,1,EQ,1,0,IF ") ;
				# if incomplete data = 1 -> 0 = no crit TICK
				# else: if case$casecount_state = 2 -> 1 = crit TICK
				$def[$casecount_int] .= rrd::cdef("tc_case".$casecount."_state_critical", "tc_case".$casecount."_incomplete,1,EQ,0,tc_case".$casecount."_state,2,EQ,2,0,IF,IF") ;

				$def[$casecount_int] .= "TICK:tc_case".$casecount."_state_warning".$col_warning.$tick_opacity_warn.":".$tick_frac.":'step/case warning ' " ;
				$def[$casecount_int] .= "TICK:tc_case".$casecount."_state_critical".$col_critical.$tick_opacity_crit.":".$tick_frac.":'step/case critical ' " ;
			}
		}

		# Suite last check time: black vertical line #################################
		$def[$casecount_int] .= "VRULE:".$NAGIOS_TIMET."#000000: ";

		# append CPU/MEM graph (defined above) ################################################
                if ($graph_cpu or $graph_mem) {
                        $def[$casecount_int] .= rrd::comment(" \\n");
                        $def[$casecount_int] .= rrd::comment("Host Statistics\:\\n");

		        if ($graph_cpu == "load" ) {
				# Load is usually a much lower value than usage (%) -> scale the right axis with factor 10
		                $opt[$casecount_int] .= " --right-axis \"0.1:0\" --right-axis-label \"CPU wd\" ";
		        } else {
		                $opt[$casecount_int] .= " --right-axis \"1:0\" --right-axis-label \"CPU Usage\" ";
		        }
                }
		if ( $graph_cpu ) {
			$def[$casecount_int] .= $rrddef_cpu;	
		}
 		if ( $graph_mem ) {
			$def[$casecount_int] .= $rrddef_mem;	
		}

	} # if (preg_match('/^c_(\d+)_([a-zA-Z0-9].*)/', $VAL['LABEL'], $c_matches))
}

#logit(print_r(@def), $DEBUG, $debug_log);
#throw new Kohana_exception(print_r($def,TRUE));

# Pad the string with spaces to ensure column alignment
function pad ($str, $len) {
	$padding = $len - strlen($str);
	return $str . str_repeat(" ", $padding);
}


# refill numbers with leading 0s
function lead3 ($num) {
	return str_pad($num,3,'0',STR_PAD_LEFT);
}

function logit ($msg, $debug, $debug_log) {
	if ($debug == 1) {
		error_log($msg . "\n", 3, $debug_log);
	}
}


#throw new Kohana_exception(print_r($def,TRUE));
#throw new Kohana_exception(print_r($idxm1,TRUE));


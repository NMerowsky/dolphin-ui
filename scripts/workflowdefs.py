stepDownload='stepDownload%(sep)s@RUNDOWNLOAD -c @CONFIG -f @FASTQDUMP -u @USERNAME -r %(runparamsid)s -o @OUTDIR -j @JOB%(sep)s60'
stepCheck='stepCheck%(sep)s@RUNCHECK -c @CONFIG -i @INPUT -u @USERNAME -b @BARCODES -w @WKEY -p %(runparamsid)s -d @DBCOMMCMD -a @ADAPTER -t @TRIM -o @OUTDIR -r %(resume)s -s stepCheck -j @JOB%(sep)s60'
stepSeqMapping='stepSeqMapping%(indexname)s%(sep)s@RUNSEQMAPPING -i @INPUT -a @DOLPHIN -d @SPAIRED -m @SAMTOOLS -o @OUTDIR -b @PARAM%(indexname)s -c @BOWTIE2CMD -s stepSeqMapping%(indexname)s -r @ADVPARAMS -j @JOB%(sep)s60'
stepBarcode='stepBarcode%(sep)s@RUNBARCODE -c @CONFIG -i @INPUT -d @SPAIRED -b @BARCODES -o @OUTDIR -c @SINGLEBSPLITTERCMD:@PAIREDBSPLITTERCMD -s stepBarcode -j @JOB%(sep)s60'
stepGetTotalReads='stepGetTotalReads%(sep)s@RUNGETTOTALREADS -c @CONFIG -b @BARCODES -r %(runparamsid)s -p @SPAIRED -o @OUTDIR -u @USERNAME -j @JOB%(sep)s60'
stepBackupS3='stepBackupS3%(sep)s@RUNBACKUPS3 -c @CONFIG -a %(amazonupload)s -b @BARCODES -r %(runparamsid)s -p @SPAIRED -o @OUTDIR -u @USERNAME -j @JOB%(sep)s20'
stepFastQC='stepFastQC%(sep)s@PERL @RUNFASTQC -b @BARCODES -o @OUTDIR -p @FASTQCPROG -s stepFastQC -j @JOB%(sep)s60'
stepMergeFastQC='stepMergeFastQC%(sep)s@PERL @RUNFASTQCMERGE -p @PUBDIR -w @WKEY -o @OUTDIR%(sep)s10'
stepAdapter='stepAdapters%(sep)s@RUNADAPTER -d @SPAIRED -pr @PREVIOUSADAPTER -o @OUTDIR -a @ADAPTER -c @MATICCMD -s stepAdapter -j @JOB%(sep)s60'
stepQuality='stepQuality%(sep)s@RUNQUALITY -d @SPAIRED -pr @PREVIOUSQUALITY -o @OUTDIR -q @QUALITY -c @MATICCMD -s stepQuality -j @JOB%(sep)s60'
stepTrim='stepTrim%(sep)s@RUNTRIM -t @TRIM -d @TRIMPAIRED -p @PREVIOUSTRIM -o @OUTDIR -c @TRIMMERCMD -s stepTrim -j @JOB%(sep)s60'
stepRSEM='stepRSEM%(sep)s@RUNRSEM -cm @RSEMCMD -co @CONVERTRSEM -r %(rsemref)s -d @SPAIRED -g %(genome_bam)s -pub @PUBDIR -w @WKEY -pa @PARAMSRSEM -sa @SAMTOOLS -pr %(previousrsem)s -o @OUTDIR  -ba %(bamsupport)s -bo @BOWTIEPATH -se stepRSEM -j @JOB%(sep)s240'
stepRSEMCount='stepRSEMCount%(g_i)s%(t_e)s%(sep)s@RUNRSEMCOUNT -p @PUBDIR -w @WKEY -o @OUTDIR -t %(t_e)s -g %(g_i)s -s stepRSEMCount%(g_i)s%(t_e)s -j @JOB%(sep)s10'
stepTophat='stepTophat2%(sep)s@PERL @RUNTOPHAT2 -o @OUTDIR -g %(gtf)s -d @SPAIRED -pub @PUBDIR -w @WKEY -pa @PARAMSTOPHAT -pr @PREVIOUSPIPE -t @TOPHAT2CMD -b %(bowtie2index)s -sa @SAMTOOLS -se stepTophat2 -j @JOB%(sep)s240'
stepAlignment='stepAlignment%(type)s%(sep)s@PERL %(script_command)s -a %(type)s -o @OUTDIR -d @SPAIRED -pub @PUBDIR -w @WKEY -pa %(addparameters)s -pr @PREVIOUSPIPE -co %(run_command)s -i %(indexref)s -sa @SAMTOOLS -se stepAlignment%(type)s -j @JOB%(sep)s240'
stepSplit='stepSplit%(sep)s@PERL @RUNSPLIT -o @OUTDIR -d @SPAIRED -p @PREVIOUSSPLIT -n @SPLIT -se stepSplit -j @JOB%(sep)s50'
stepMergeBAM='stepMergeBAM%(type)s%(sep)s@PERL @RUNMERGEBAM -o @OUTDIR -mergeall @MERGEALL -t %(type)s -d @SPAIRED -sa @SAMTOOLS -se stepMergeBAM%(type)s -j @JOB%(sep)s50'
stepATACPrep='stepATACPrep%(sep)s@RUNATACPREP -c @CUTADJUST -b @BEDTOOLSATAC -g @GENOMESIZE -t %(type)s -o @OUTDIR -s stepATACPrep -j @JOB%(sep)s60'
stepMACS='stepMACS%(sep)s@RUNMACS -a @MACSCMD -i "@CHIPINPUT" -e "@EXTRAPARAMS" -t %(type)s -o @OUTDIR -s stepMACS -j @JOB%(sep)s60'
stepAggregation='stepAggregation%(sep)s@RUNAGGREGATION -o @OUTDIR -a @ACT -b @BTOOLSGENCOV -c @INTOPDF -t %(type)s -p @PREVIOUSPIPE -g @GENOMESIZE -r @REFACT -s stepAggregation -j @JOB%(sep)s10'
stepIGVTDF='stepIGVTDF%(type)s%(sep)s@RUNIGVTDF -o @OUTDIR -g @GENOMEFASTA -pa @SPAIRED %(paramExtFactor)s -pu @PUBDIR -w @WKEY -l @TSIZE -sa @SAMTOOLS -t %(type)s -i @IGVTOOLS -se stepIGVTDF%(type)s -j @JOB%(sep)s60'
stepBam2BW='stepBam2BW%(type)s%(sep)s@RUNBAM2BW -o @OUTDIR -g @GENOMESIZE -p @PUBDIR -wk @WKEY -c @RUNCOVERAGE -t %(type)s -wi @WIGTOBIGWIG -sa @SAMTOOLS -se stepBam2BW%(type)s -j @JOB%(sep)s60'
stepDeeptools='stepDeeptools%(sep)s@RUNDEEPTOOLS -d @DEEPTOOLSHEAT -m @MERGEALLSAMPS -k @KMEANS -c @COMPDEEPTOOLS -g @GENOMEBED -p @PLOTTYPE -r @REFTYPE -a @AFTER -b @BEFORE -l @LENGTHBODY -n @NAMERUN -t %(type)s -o @OUTDIR -s stepDeeptools -j @JOB%(sep)s60'
stepPicard='stepPicard%(type)s%(metric)s%(sep)s@PERL @RUNPICARD -o @OUTDIR -n %(metric)s -pu @PUBDIR -w @WKEY -t %(type)s -r @REFFLAT -pi @PICARDCMD -sa @SAMTOOLS -se stepPicard%(type)s%(metric)s -j @JOB%(sep)s30'
stepMergePicard='stepMergePicard%(type)s%(sep)s@PERL @RUNMERGEPICARD -o @OUTDIR -m @MERGEPDFCMD -p @PUBDIR -w @WKEY -t %(type)s %(sep)s30'
stepPCRDups='stepPCRDups%(type)s%(sep)s@PERL @PCRDUPS -o @OUTDIR -m @MERGEPDFCMD -p @PUBDIR -w @WKEY -t %(type)s -se stepPCRDups%(type)s -j @JOB%(sep)s30'
stepBamToFastq='stepBamToFastq%(type)s%(sep)s@PERL @RUNBAMTOFASTQ -c @BTOOLSBAMTOFASTQ -p @SPAIRED -o @OUTDIR -t %(type)s -sa @SAMTOOLS -se stepBamToFastq%(type)s -j @JOB%(sep)s30'
stepRSEQC='stepRSEQC%(type)s%(sep)s@RUNRSEQC -b @BED12FILE -r @RSEQCCMD -t %(type)s -o @OUTDIR -s stepRSEQC%(type)s -j @JOB%(sep)s10'
stepMergeRSEQC='stepMergeRSEQC%(type)s%(sep)s@PERL @RUNMERGERSEQC -o @OUTDIR -p @PUBDIR -w @WKEY -t %(type)s %(sep)s30'
stepBSMap='stepBSMap%(sep)s@RUNBSMAP -binpath @BSMAPCMD -digestion @DIGESTION -dspaired @SPAIRED -outdir @OUTDIR -params @BSMAPPARAM -previous @PREVIOUSPIPE -ref @GENOMEFASTA -samtools @SAMTOOLS -servicename stepBSMap -j @JOB%(sep)s30'
stepMCall='stepMCall%(sep)s@RUNMCALL -binpath @BIN/mcall -previous %(type)s -params @MCALLPARAM -ref @GENOMEFASTA -o @OUTDIR -servicename stepMCall -j @JOB%(sep)s30'
stepMethylKit='stepMethylKit%(sep)s@RUNMETHYLKIT -sa @INPUT -g @GBUILD -m @MINCOVERAGE -to @TOPN -ste @STEP_SIZE -ti @TILE_SIZE -o @OUTDIR -strand @STRAND -r @RSCRIPT -p @PUBDIR -w @WKEY -se stepMethylKit -j @JOB%(sep)s30'
stepDiffMeth='stepDiffMeth%(sep)s@RUNDIFFMETH -sa @COLS%(diffmeth_name)s -c @CONDS%(diffmeth_name)s -o @OUTDIR -n %(diffmeth_name)s -b @BED12FILE -r @RSCRIPT -p @PUBDIR -w @WKEY %(sep)s30'
stepCounts='stepCounts%(sep)s@RUNCOUNTS -m @MAPNAMES -i @INDEXCMD -o @OUTDIR -g @GCOMMONDB -p @PUBDIR -w @WKEY -b @MAKEBED -c @BEDTOOLSCMD -s stepCounts -j @JOB%(sep)s10'
stepDESeq2='stepDESeq2%(deseq_name)s%(sep)s@RUNDESEQ2 -c @COLS%(deseq_name)s -pu @PUBDIR -da @DATASET%(deseq_name)s -w @WKEY -dc @CONDS%(deseq_name)s -r @RSCRIPT -o @OUTDIR -n %(deseq_name)s -e @HEATMAP%(deseq_name)s -t @FITTYPE%(deseq_name)s -pa @PADJ%(deseq_name)s -f @FOLDCHANGE%(deseq_name)s -s stepDESeq2%(deseq_name)s -j @JOB%(sep)s10'
stepHaplotype='stepHaplotype%(sep)s@RUNHAPLOTYPE -o @OUTDIR -g @GENOMEFASTA -sa @SAMTOOLS -bed @BEDTOOLS -haplobed @HAPLOBED -haplocmd @HAPLOTYPECMD -multicmd @MULTIINTER -pi @PICARDCMD -smctfc @SMCTFC -smctfe @SMCTFE -mb @MBQS -mrp @MRPAS -mri @MRIRPS -common @COMMON -cl @CLINICAL -enh @ENHANCER -pro @PROMOTER -mo @MOTIFS -compare @PEAKS -se stepHaplotype -merge @MERGEALL -cust @CUSTOMBED -t %(type)s -j @JOB%(sep)s10'
stepSummary='stepSummary%(sep)s@RUNSUMMARY -c @CONFIG -s @SAMTOOLSFLAG -u @USERNAME -p @PUBDIR -w @WKEY -o @OUTDIR%(sep)s10'
stepAlignmentCount='stepAlignmentCount%(type)s%(sep)s@RUNALIGNMENTCOUNT -c @CONFIG -s @SAMTOOLSFLAG -u @USERNAME -t %(type)s -p @PUBDIR -w @WKEY -o @OUTDIR%(sep)s10'
stepClean='stepClean%(sep)s@RUNCLEAN -c @CONFIG -l %(level)s -u @USERNAME -p @PUBDIR -w @WKEY -d @DBCOMMCMD  -o @OUTDIR%(sep)s10'


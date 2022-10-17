MD=$(wildcard *.md)
PDFC=pdflatex
TARGET = out/problemas.pdf

all: out $(TARGET) rm_aux


#out/%.pdf: %.md Makefile inc/cabecera.tex
#	@TEX=$(<:.md=.tex); \
#		cat inc/cabecera.tex >$$TEX; \
#		php md2tex.php $< >>$$TEX; \
#		cat inc/pie.tex >>$$TEX; \
#		$(PDFC) $$TEX; \
#		$(PDFC) $$TEX; 
#	mv $(<:.md=.pdf) out/

out/problemas.pdf: $(MD) inc/cabecera.tex inc/pie.tex Makefile
	cat inc/cabecera.tex 		>problemas.tex
	echo "\\\chapter{Problemas de 1º ESO}\n" >>problemas.tex
	php md2tex.php 1eso.md 		>>problemas.tex
	echo "\\\chapter{Problemas de 2º ESO}\n" >>problemas.tex
	php md2tex.php 2eso.md 		>>problemas.tex
	echo "\\\chapter{Problemas de 3º ESO}\n" >>problemas.tex
	php md2tex.php 3eso.md 		>>problemas.tex
	echo "\\\chapter{Problemas de 4º ESO}\n" >>problemas.tex
	php md2tex.php 4eso.md 		>>problemas.tex
	echo "\\\chapter{Problemas de 1º Bachillerato}\n" >>problemas.tex
	php md2tex.php 1bto.md 		>>problemas.tex
	cat inc/pie.tex 			>>problemas.tex
	$(PDFC) problemas.tex
	$(PDFC) problemas.tex
	mv problemas.pdf out/

out:
	@mkdir out

rm_aux:
	@# e is extension of file (.ext)
	@for e in aux bbl bcf blg log out run.xml toc ; do \
		rm -f *.$$e ; \
	done
	@rm -f 1bto.tex

clean: rm_aux
	rm -f $(TARGETS)
	rm -f aux/*

module Tpc
  module Blocks
    class Tikz < Liquid::Block
      def initialize(tag_name, markup, tokens)
        super
      end
      def render(context)
        site = context.registers[:site]
        name = Digest::MD5.hexdigest(super)
        if !File.exists?(File.join(site.source, "tikz/#{name}.png"))
          tex = File.join(site.source, ".tikz-temp/tikz.tex")
          FileUtils.mkdir_p(File.dirname(tex))
          File.open(tex, 'w') { |f| f.write(super) }
          puts %x[
            set -e
            cd #{site.source}
            mkdir -p tikz
            mkdir -p .tikz-temp
            cd .tikz-temp
            cat ../_latex/header.tex > doc.tex
            cat tikz.tex >> doc.tex
            echo '\\end{document}' >> doc.tex
            latex -halt-on-error -interaction=nonstopmode doc.tex >/dev/null
            dvips -o doc.ps doc.dvi
            echo quit | \\
              gs -q -dNOPAUSE -sDEVICE=ppmraw -sOutputFile=- -r300 doc.ps | \\
              pnmalias -bgcolor rgb:ff/ff/ff -falias -fgcolor rgb:00/00/00 -weight 0.6 | \\
              pnmcrop -white | \\
              pnmtopng -interlace > doc.png
            mv doc.png ../tikz/#{name}.png
            cd ..
          ]
          raise 'failed to compile Tikz' if !$?.exitstatus
        end
        site.static_files << Jekyll::StaticFile.new(
          site, site.source, 'tikz', "#{name}.png"
        )
        "<p class='tikz'><img src='/tikz/#{name}.png' alt='tikz' width='80%'/></p>"
      end
    end
  end
end

Liquid::Template.register_tag('tikz', Tpc::Blocks::Tikz)

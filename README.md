# OpenSeaDragon TEI Viewer
OpenSeaDragon TEI Viewer is a plugin for Omeka Classic which makes it possible to view images of manuscripts and their corresponding transcriptions side by side. The screenshot above shows the manuscript pages in the center column, a transcription on the left, and metadata on the right. At the top of the center column is a toolbar with controls to view the next or previous page in the manuscript, and adjust the zoom level.

In Omeka, Items are assigned Item Types which in turn determine the scope and structure of those records. OpenSeaDragon TEI Viewer functions by overriding the public display of items matching an Item Type you provide. In the plugin configuration area, any number of viewers with different settings can be created for different Item Types. These viewers can display multiple images in a pannable, zoomable gallery, or if a TEI file is attached to the item record and an XSLT file is attached to the configuration, a text transcription panel will also be displayed in the viewer, rendering the TEI in HTML.

###Installing the Plugin
OpenSeaDragon TEI Viewer is installed just like any other plugin, by decompressing the zip file, and copying the resulting folder into Omeka’s plugins folder on the server. Next, log into the admin interface in your Omeka installation and click on Plugins at the top, and under OpenSeadragon TEI Viewer, click Install, and then Activate.

###Configuration
Now that the plugin has been activated, TEI Viewer should appear in the menu bar on the left. Click on it to go into the plugin configuration area.

Click Add a Viewer to bring up the Add Viewer form. Under Viewer Name, enter Text, under Upload XSLT Transformation, upload a copy of the default.xslt stylesheet file included in the plugin folder, and under Select Item Type to Apply the Viewer to, choose Text. Then click Save.

###Adding Items
Add a new item by clicking Items in the left-hand menu, and then click Add an Item in the items listing that will appear. On the Dublin Core Tab, provide a Title and some other metadata in the other available fields. Click on the Item Type Metadata tab, and under Item Type, choose Text. Next, click on Files and then upload the images of your text and a TEI transcription of that text. Then, click Add Item.

The default.xsl stylesheet included with the plugin comprises a very basic set of templates. While these can be customized, removed, or extended to meet your needs, here is what these templates assume by default:

* Pages: Each page in the transcription are enclosed in a <div> tag with type=”page”. These <div>s are arranged one after another in the TEI file, and are numbered numerically using the n attribute (e.g., n=”1”, n=”2”, n=”3”, and so on)
* Paragraphs: The text on each page is grouped using <p> tags.
* Hyperlinks: These can be created in the TEI with a <ref> tag with type=”url”. Add the desired URL as a target attribute to the <ref> tag, like so:
> <ref type=”url” target=”http://www.google.com”>Google</ref>
Deleted Text:  <del> tags in TEI are rendered as <span class=”deleted-text”> and styled via CSS with a strikethrough.
* Added Text: <add> tags in TEI are rendered as <span class=”add”>. place=”above” can be added to the <add> tag to raise the baseline of the text to superscript position.
Underlined Text: <hi rend=”underline”> is rendered as <span class=”underline”>.
* Note: Inline notes can be added to the TEI with a <note> tag. This inserts a [*] button which when clicked opens up a small popup dialog containing the note.
* Line break: <lb> in TEI is rendered as <br> in HTML
